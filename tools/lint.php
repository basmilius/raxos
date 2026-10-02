<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$config = simplexml_load_file($root . '/phpunit.xml');
$directories = [$root . '/tests', $root . '/tools'];

foreach ($config->testsuites->testsuite as $suite) {
    $library = basename(dirname((string)$suite->directory));
    $directories[] = $root . '/' . $library . '/src';
    $directories[] = $root . '/' . $library . '/tests';
}

$paths = [];

foreach ($directories as $directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));

    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $paths[] = $file->getPathname();
    }
}

$process = proc_open([PHP_BINARY, '-l', ...$paths], [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes);

while (($line = fgets($pipes[1])) !== false) {
    if (!str_starts_with($line, 'No syntax errors detected')) {
        fwrite(STDERR, $line);
    }
}

fclose($pipes[1]);
$exitCode = proc_close($process);
printf("Linted %d PHP files.\n", count($paths));
exit($exitCode);
