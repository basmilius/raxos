<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$config = simplexml_load_file($root . '/phpunit.xml');
$libraries = [];

foreach ($config->testsuites->testsuite as $suite) {
    $libraries[] = (string)$suite['name'];
}

$actualLibraries = array_map(static fn(string $path): string => basename(dirname($path)), glob($root . '/*/composer.json'));
$expectedLibraries = $libraries;
sort($actualLibraries);
sort($expectedLibraries);

if ($actualLibraries !== $expectedLibraries) {
    fwrite(STDERR, "Every library must have a workspace test suite.\n");
    exit(1);
}

$failed = false;

foreach (['.', ...$libraries] as $library) {
    $directory = $root . '/' . $library;
    $process = proc_open(['composer', 'validate', '--strict', '--no-check-all', '--no-check-publish'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $directory);
    $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    if (proc_close($process) !== 0) {
        fwrite(STDERR, "{$library}: {$output}");
        $failed = true;
    }

    if ($library !== '.') {
        $hasTests = false;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory . '/tests', FilesystemIterator::SKIP_DOTS)) as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $hasTests = $hasTests || str_ends_with($file->getFilename(), 'Test.php');

            foreach (token_get_all(file_get_contents($file->getPathname())) as $token) {
                if (is_array($token) && $token[0] === T_DOC_COMMENT) {
                    fwrite(STDERR, "{$file->getPathname()}: PHPDocs are not allowed in tests.\n");
                    $failed = true;
                }
            }
        }

        if (!$hasTests) {
            fwrite(STDERR, "{$library}: no Pest tests found.\n");
            $failed = true;
        }
    }
}

printf("Validated workspace and %d libraries.\n", count($libraries));
exit($failed ? 1 : 0);
