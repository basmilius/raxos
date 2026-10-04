<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\Collection\ArrayList;
use Raxos\Collection\LazySequence;
use Raxos\Database\Connection\SQLite;
use Raxos\Database\Db;
use Raxos\Database\Orm\Attribute\Column;
use Raxos\Database\Orm\Attribute\PrimaryKey;
use Raxos\Database\Orm\Attribute\Table;
use Raxos\Database\Orm\Model;
use Raxos\RateLimit\Store\RedisRateLimiterStore;

require dirname(__DIR__) . '/vendor/autoload.php';

#[Table('raxos_bench_rows')]
final class FeatureBenchmarkRow extends Model
{
    #[PrimaryKey]
    public int $id;

    #[Column]
    public string $payload;
}

final class FeatureCountingRedis extends RedisCache
{
    public int $evaluations = 0;

    public int $existenceReads = 0;

    public function eval(string $script, array $keys = [], array $args = []): mixed
    {
        ++$this->evaluations;

        return parent::eval($script, $keys, $args);
    }

    public function exists(string $key): bool
    {
        ++$this->existenceReads;

        return parent::exists($key);
    }
}

if (($argv[1] ?? null) === 'worker') {
    $mode = $argv[2];
    $count = (int)$argv[3];
    $source = static function () use ($count): Generator {
        for ($id = 1; $id <= $count; ++$id) {
            yield ['id' => $id, 'payload' => str_repeat('x', 128)];
        }
    };

    if ($mode === 'keyset') {
        $connection = SQLite::createFromInMemory();
        $connection->connect();
        Db::register($connection);
        $connection->execute('CREATE TABLE raxos_bench_rows (id INTEGER PRIMARY KEY, payload TEXT)');
        $connection->transactional(static function () use ($connection, $source): void {
            $insert = $connection->pdo->prepare('INSERT INTO raxos_bench_rows VALUES (?, ?)');

            foreach ($source() as $row) {
                $insert->execute([$row['id'], $row['payload']]);
            }
        });
        FeatureBenchmarkRow::select()->limit(1)->array();
        $connection->cache->flushAll();
        $connection->logger->disable();
    } else {
        LazySequence::from([])->toArray();
        new ArrayList([]);
    }

    gc_collect_cycles();
    memory_reset_peak_usage();
    $baseline = memory_get_usage();
    $started = hrtime(true);
    $items = match ($mode) {
        'eager' => ArrayList::of($source()),
        'lazy' => LazySequence::from($source),
        'keyset' => FeatureBenchmarkRow::select()->lazyById(128),
        default => throw new InvalidArgumentException('Unknown benchmark mode.')
    };
    $rows = 0;
    $checksum = 0;

    foreach ($items as $row) {
        ++$rows;
        $checksum += $row instanceof Model ? $row->id : $row['id'];
    }

    $elapsed = (hrtime(true) - $started) / 1e6;
    $peak = memory_get_peak_usage() - $baseline;
    unset($row, $items);
    gc_collect_cycles();
    $result = [
        'mode' => $mode,
        'rows' => $rows,
        'checksum' => $checksum,
        'peak_php_bytes' => $peak,
        'retained_php_bytes' => memory_get_usage() - $baseline,
        'elapsed_ms' => round($elapsed, 3)
    ];

    if ($mode === 'keyset') {
        $result['batch_size'] = 128;
        // Count queries in a separate pass so retained log events do not distort the heap measurement.
        $connection->logger->enable();

        foreach (FeatureBenchmarkRow::select()->lazyById(128) as $countedRow) {
        }
        unset($countedRow);
        $result['queries'] = $connection->logger->count();
        $result['cached_models'] = new ReflectionProperty($connection->cache, 'size')->getValue($connection->cache);
    }

    echo json_encode($result, JSON_THROW_ON_ERROR), "\n";
    exit;
}

$result = [
    'timestamp_utc' => gmdate('c'),
    'php' => PHP_VERSION,
    'platform' => PHP_OS_FAMILY . ' ' . php_uname('m'),
    'conditions' => 'Fresh PHP processes; 128-byte payloads; keyset uses SQLite; Heap pass disables logging; query counts use a separate pass. PHP heap excludes driver allocations and RSS.',
    'iteration' => []
];

foreach (['eager' => [50000], 'lazy' => [5000, 50000], 'keyset' => [5000, 50000]] as $mode => $counts) {
    foreach ($counts as $count) {
        $process = proc_open([PHP_BINARY, __FILE__, 'worker', $mode, (string)$count], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        if (proc_close($process) !== 0) {
            throw new RuntimeException($error);
        }
        $result['iteration'][] = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
    }
}

if (getenv('RAXOS_REDIS_HOST')) {
    $prefix = 'raxos-feature-bench:' . bin2hex(random_bytes(8));
    $cache = new FeatureCountingRedis($prefix, getenv('RAXOS_REDIS_HOST'), (int)(getenv('RAXOS_REDIS_PORT') ?: 0));
    $nativeRedis = new ReflectionProperty($cache, 'connection')->getValue($cache);
    $nativeRedis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    $key = $prefix . ':value';

    try {
        $cache->setex($key, false, 60);
        $cache->evaluations = $cache->existenceReads = 0;
        $entry = $cache->lookup($key);
        $result['redis']['lookup'] = ['eval_calls' => $cache->evaluations, 'exists_calls' => $cache->existenceReads, 'found_false' => $entry->found && $entry->value === false];
        $tagged = $cache->tags(['catalog']);
        $tagged->set('item', 'value', 60);
        $cache->evaluations = $cache->existenceReads = 0;
        $value = $tagged->remember('item', 60, static fn(): never => throw new LogicException('Cache hit must not compute.'));
        $result['redis']['tagged_hit'] = ['eval_calls' => $cache->evaluations, 'exists_calls' => $cache->existenceReads, 'value' => $value];
        $cache->evaluations = $cache->existenceReads = 0;
        $snapshot = new RedisRateLimiterStore($cache, $prefix . ':rate:')->snapshot('read', 60);
        $result['redis']['rate_snapshot'] = ['eval_calls' => $cache->evaluations, 'operations' => $snapshot['operations']];
    } finally {
        $keys = $cache->keys($prefix . '*');

        if ($keys !== []) {
            $cache->del(...$keys);
        }
    }
}

$failures = [];

foreach ($result['iteration'] as $row) {
    if ($row['checksum'] !== $row['rows'] * ($row['rows'] + 1) / 2) {
        $failures[] = 'Iteration lost or repeated rows.';
    }

    if ($row['mode'] !== 'eager' && $row['peak_php_bytes'] > 8 * 1024 * 1024) {
        $failures[] = $row['mode'] . ' exceeded the bounded PHP heap budget.';
    }

    if ($row['mode'] === 'keyset' && ($row['cached_models'] !== 0 || $row['queries'] !== (int)ceil($row['rows'] / $row['batch_size']))) {
        $failures[] = 'Keyset retained identities or issued unexpected queries.';
    }
}

foreach ($result['redis'] ?? [] as $name => $row) {
    if ($row['eval_calls'] !== 1 || ($row['exists_calls'] ?? 0) !== 0) {
        $failures[] = $name . ' used more than one Redis snapshot.';
    }
}

if (isset($result['redis']) && !$result['redis']['lookup']['found_false']) {
    $failures[] = 'Lookup confused cached false with a missing value.';
}
$result['checks'] = ['passed' => $failures === [], 'failures' => $failures, 'timings_are_gated' => false];
echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), "\n";
exit(in_array('--check', $argv, true) && $failures !== [] ? 1 : 0);
