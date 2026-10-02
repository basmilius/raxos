<?php
declare(strict_types=1);

use Raxos\Database\Connection\SQLite;
use Raxos\Database\Db;
use Raxos\Database\Logger\Event;
use Raxos\Database\Logger\QueryEvent;
use Raxos\Http\HttpResponse;
use Raxos\Http\HttpSendFile;
use Raxos\Http\Response\NoContentHttpResponse;
use Raxos\Mail\Util\PublicSuffixList;
use Raxos\Router\DynamicRouter;
use Raxos\Search\Query\Lexer;
use RaxosTests\Cache\RecordingRedisCache;
use RaxosTests\Database\ChildModel;
use RaxosTests\Database\ParentModel;

require dirname(__DIR__) . '/vendor/autoload.php';

if (($argv[1] ?? '') === 'file') {
    $start = hrtime(true);
    new HttpSendFile($argv[2])->handle(null);
    fwrite(STDERR, json_encode(['elapsed_ms' => (hrtime(true) - $start) / 1e6], JSON_THROW_ON_ERROR));
    exit;
}

require dirname(__DIR__) . '/database/tests/Fixtures/Models.php';
require dirname(__DIR__) . '/cache/tests/Fixtures/RecordingRedisCache.php';

/**
 * @param callable $fn
 * @param int $runs
 * @return float
 * @author Bas Milius <bas@mili.us>
 * @since 3.2.0
 */
function benchmarkMedian(callable $fn, int $runs = 7): float
{
    $fn();
    $times = [];

    for ($i = 0; $i < $runs; ++$i) {
        $start = hrtime(true);
        $fn();
        $times[] = (hrtime(true) - $start) / 1e6;
    }

    sort($times);
    return round($times[intdiv($runs, 2)], 4);
}

$result = [
    'date' => date('Y-m-d'),
    'php' => PHP_VERSION,
    'os' => PHP_OS_FAMILY,
    'architecture' => php_uname('m'),
    'opcache_cli' => ini_get('opcache.enable_cli'),
    'jit' => ini_get('opcache.jit'),
    'conditions' => ['warmup_runs' => 1, 'lexer_runs' => 5, 'suffix_runs' => 7, 'router_runs' => 5, 'json_runs' => 7, 'file_runs' => 5, 'database' => 'SQLite :memory:'],
];

foreach ([1_000, 4_000, 16_000, 64_000] as $length) {
    foreach (['ascii' => 'a', 'utf8' => 'é'] as $encoding => $character) {
        $query = str_repeat($character, $length);
        $result['lexer'][] = ['characters' => $length, 'encoding' => $encoding, 'median_ms' => benchmarkMedian(static fn(): array => new Lexer($query)->tokenize(), 5)];
    }
}

$result['suffix_load_ms'] = benchmarkMedian(static fn(): mixed => PublicSuffixList::load(true));
$suffixes = new ReflectionProperty(PublicSuffixList::class, 'suffixes');
$result['suffix_count_before_reload'] = count($suffixes->getValue());
PublicSuffixList::load(true);
$result['suffix_count_after_reload'] = count($suffixes->getValue());

foreach ([100, 400, 1_600] as $count) {
    $result['dynamic_router_registration_ms'][$count] = benchmarkMedian(static function () use ($count): void {
        $router = new DynamicRouter();

        for ($i = 0; $i < $count; ++$i) {
            $router->get('/r' . $i . '/$id', static fn(int $id): HttpResponse => new NoContentHttpResponse());
        }
    }, 5);
}

foreach ([1_024 * 1_024, 5 * 1_024 * 1_024] as $size) {
    $json = json_encode(['data' => str_repeat('x', $size)], JSON_THROW_ON_ERROR);
    $result['json'][] = [
        'bytes' => strlen($json),
        'validate_decode_ms' => benchmarkMedian(static function () use ($json): void {
            if (json_validate($json)) {
                json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            }
        }),
        'decode_ms' => benchmarkMedian(static fn(): mixed => json_decode($json, true, 512, JSON_THROW_ON_ERROR)),
        'scope' => 'isolated parsing operations; not HTTP request latency',
    ];
}

$connection = new SQLite('sqlite::memory:');
Db::register($connection);
$connection->connect();
$connection->pdo->exec('CREATE TABLE parents (id INTEGER PRIMARY KEY, external_key INTEGER, name TEXT); CREATE TABLE children (id INTEGER PRIMARY KEY, parent_id INTEGER, parent_key INTEGER)');
$insertParent = $connection->pdo->prepare('INSERT INTO parents VALUES (?,?,?)');
$insertChild = $connection->pdo->prepare('INSERT INTO children VALUES (?,?,?)');

for ($i = 1; $i <= 100; ++$i) {
    $insertParent->execute([$i, $i, 'parent']);
    $insertChild->execute([$i, $i, $i]);
}

ParentModel::select()->limit(1)->array();
$connection->cache->flushAll();
$connection->logger->enable();
$events = new ReflectionProperty($connection->logger, 'events');
$countQueries = static fn(): int => count(array_filter($events->getValue($connection->logger), static fn(Event $event): bool => $event instanceof QueryEvent));
$before = $countQueries();
$rows = 0;

foreach (ParentModel::select()->eagerLoad('children')->cursor() as $parent) {
    $rows += $parent->children->count();
}

$cacheSize = new ReflectionProperty($connection->cache, 'size');
$result['orm_cursor'] = ['parents' => 100, 'children' => $rows, 'queries' => $countQueries() - $before, 'cached_models' => $cacheSize->getValue($connection->cache), 'batch_size' => 100];
unset($parent);
$connection->logger->disable();

for ($i = 101; $i <= 10_000; ++$i) {
    $insertChild->execute([$i, 1, 1]);
}

gc_collect_cycles();
$memoryBefore = memory_get_usage();
$rows = 0;

foreach (ChildModel::select()->cursor() as $child) {
    ++$rows;
}

unset($child);
gc_collect_cycles();
$result['cursor_retention'] = ['rows' => $rows, 'cached_models' => $cacheSize->getValue($connection->cache), 'held_bytes_over_baseline' => memory_get_usage() - $memoryBefore];

$file = tempnam(sys_get_temp_dir(), 'raxos-benchmark-');

try {
    file_put_contents($file, str_repeat('x', 1_024 * 1_024));
    $times = [];

    for ($i = 0; $i < 6; ++$i) {
        $process = proc_open([PHP_BINARY, __FILE__, 'file', $file], [1 => ['file', '/dev/null', 'w'], 2 => ['pipe', 'w']], $pipes);
        $output = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        if (proc_close($process) !== 0) {
            throw new RuntimeException($output);
        }

        if ($i > 0) {
            $times[] = json_decode($output, true, 512, JSON_THROW_ON_ERROR)['elapsed_ms'];
        }
    }

    sort($times);
    $result['send_file'] = ['bytes' => 1_024 * 1_024, 'default_throttle' => 0, 'median_ms' => round($times[2], 4), 'scope' => 'handler time, local file to /dev/null; excludes process startup and network'];
} finally {
    unlink($file);
}

if (getenv('RAXOS_REDIS_HOST') !== false) {
    $prefix = 'raxos-benchmark:' . bin2hex(random_bytes(8));
    $redis = new RecordingRedisCache($prefix, getenv('RAXOS_REDIS_HOST'), (int)(getenv('RAXOS_REDIS_PORT') ?: 0));

    try {
        $tagged = $redis->tags(['benchmark']);

        for ($i = 0; $i < 10_000; ++$i) {
            $tagged->set((string)$i, 'value', 60);
        }

        $start = hrtime(true);
        $tagged->flush();
        $result['tag_flush'] = ['members' => 10_000, 'eval_calls' => count($redis->batches), 'batches' => $redis->batches, 'elapsed_ms' => (hrtime(true) - $start) / 1e6, 'scope' => 'real Redis, one run, no concurrent writers'];
    } finally {
        $keys = $redis->keys($prefix . '*');

        if ($keys !== []) {
            $redis->del(...$keys);
        }
    }
} else {
    $result['tag_flush'] = ['skipped' => 'Set RAXOS_REDIS_HOST and RAXOS_REDIS_PORT to enable Redis measurements.'];
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), "\n";
