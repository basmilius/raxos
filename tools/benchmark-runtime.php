<?php
declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Raxos\Barcode\QRCode;
use Raxos\Container\Container;
use Raxos\Http\HttpRequest;
use Raxos\Reflection\ClassReflector;
use Raxos\Router\DynamicRouter;
use Raxos\Wallet\Apple\Component\Pass;
use Raxos\Wallet\Apple\PKPass;
use function RaxosTests\Wallet\unitIdentity;
use function RaxosTests\Wallet\walletZipContents;

require dirname(__DIR__) . '/vendor/autoload.php';

if ($baseline = getenv('RAXOS_BENCH_BASELINE_DIR')) {
    $loader = new ClassLoader();

    foreach (glob($baseline . '/*/composer.json') as $manifestFile) {
        $manifest = json_decode(file_get_contents($manifestFile), true, flags: JSON_THROW_ON_ERROR);

        foreach ($manifest['autoload']['psr-4'] ?? [] as $namespace => $directories) {
            foreach ((array)$directories as $directory) {
                $loader->addPsr4($namespace, dirname($manifestFile) . '/' . $directory);
            }
        }
    }

    $loader->register(prepend: true);
}

final class RuntimeBenchmarkLeaf
{

    public int $id = 1;

}

final readonly class RuntimeBenchmarkBranch
{

    public function __construct(public RuntimeBenchmarkLeaf $leaf) {}

}

final readonly class RuntimeBenchmarkRoot
{

    public function __construct(
        public RuntimeBenchmarkBranch $branch,
        public RuntimeBenchmarkLeaf $leaf
    ) {}

}

/**
 * Measures the first operation separately from the median of warmed batches; all times are milliseconds.
 *
 * @param callable(): void $operation
 * @param int $iterations
 * @return array{iterations: int, cold_ms: float, median_batch_ms: float}
 * @author Bas Milius <bas@mili.us>
 * @since 3.3.0
 */
function runtimeMeasurement(callable $operation, int $iterations): array
{
    $started = hrtime(true);
    $operation();
    $cold = (hrtime(true) - $started) / 1e6;
    $samples = [];

    for ($run = 0; $run < 8; ++$run) {
        $started = hrtime(true);

        for ($iteration = 0; $iteration < $iterations; ++$iteration) {
            $operation();
        }

        if ($run > 0) {
            $samples[] = (hrtime(true) - $started) / 1e6;
        }
    }

    sort($samples);

    return ['iterations' => $iterations, 'cold_ms' => round($cold, 4), 'median_batch_ms' => round($samples[3], 4)];
}

if (($argv[1] ?? null) === 'worker') {
    $mode = $argv[2];

    $result = match ($mode) {
        'reflection' => runtimeMeasurement(static function (): void {
            $reflector = new ClassReflector(RuntimeBenchmarkRoot::class);
            iterator_to_array($reflector->getPublicProperties());
            iterator_to_array($reflector->getConstructor()->getParameters());
        }, 10000),
        'container' => runtimeMeasurement(static function (): void {
            $service = new Container()->get(RuntimeBenchmarkRoot::class);

            if ($service->leaf->id !== 1 || $service->branch->leaf->id !== 1) {
                throw new LogicException('Autowiring lost a dependency.');
            }
        }, 2000),
        'router' => runtimeMeasurement(static function (): void {
            $router = new DynamicRouter();

            for ($route = 0; $route < 400; ++$route) {
                $router->get('/runtime/' . $route . '/$id', static fn(int $id): int => $id);
            }

            if ($router->resolve(HttpRequest::create(uri: '/runtime/399/42'))->result !== 42) {
                throw new LogicException('The compiled route did not resolve.');
            }
        }, 1),
        'barcode' => runtimeMeasurement(static function (): void {
            $barcode = new QRCode('https://example.org/tickets/01J8QYWHDKS4M4WWB50FEPH9D8');
            $svg = $barcode->renderSvg(scale: 4);
            $png = $barcode->renderPng(scale: 4);

            if (!str_contains($svg, '<svg') || !str_starts_with($png, "\x89PNG")) {
                throw new LogicException('Barcode rendering returned invalid output.');
            }
        }, 10),
        'wallet' => null,
        default => throw new InvalidArgumentException('Unknown runtime benchmark mode.')
    };

    if ($mode === 'wallet') {
        require dirname(__DIR__) . '/wallet/tests/Fixtures/Units.php';
        $identity = unitIdentity();
        $source = tempnam(sys_get_temp_dir(), 'raxos-wallet-benchmark-');
        file_put_contents($source, random_bytes(64 * 1024));
        $checked = false;

        try {
            $result = runtimeMeasurement(static function () use ($identity, $source, &$checked): void {
                $pass = new PKPass($identity, new Pass('Benchmark pass', 'Raxos', 'benchmark'));

                try {
                    for ($attachment = 0; $attachment < 16; ++$attachment) {
                        $pass->file($attachment . '.bin', $source);
                    }

                    $pass->sign();
                    $pass->close();
                    $binary = $pass->binary();

                    if (!$checked) {
                        if (count(walletZipContents($binary)) !== 19) {
                            throw new LogicException('The signed pass lost an attachment.');
                        }

                        $checked = true;
                    }
                } finally {
                    $pass->delete();
                }
            }, 1);
        } finally {
            unlink($source);
        }
    }

    echo json_encode($result, JSON_THROW_ON_ERROR), "\n";
    exit;
}

$result = [
    'timestamp_utc' => gmdate('c'),
    'php' => PHP_VERSION,
    'platform' => PHP_OS_FAMILY . ' ' . php_uname('m'),
    'opcache_cli' => ini_get('opcache.enable_cli'),
    'source' => $baseline ? 'Exported HEAD sources; current third-party dependencies and autoloaded function files.' : 'Working tree',
    'conditions' => 'One fresh PHP process per workload. First operation includes lazy class loading; one warmup batch and seven timed batches. Wallet uses 16 disk attachments of 64 KiB, ZIP compression and a local test signature; key generation and fixture setup excluded. First-wallet validation is excluded from warm timings.',
    'workloads' => []
];

foreach (['reflection', 'container', 'router', 'barcode', 'wallet'] as $mode) {
    $process = proc_open([PHP_BINARY, __FILE__, 'worker', $mode], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    if (proc_close($process) !== 0) {
        throw new RuntimeException($error);
    }

    $result['workloads'][$mode] = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), "\n";
