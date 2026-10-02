<?php
declare(strict_types=1);

require dirname(__DIR__) . "/vendor/autoload.php";

foreach (glob(dirname(__DIR__) . "/*/tests/Fixtures/*.php") ?: [] as $fixture) {
    require_once $fixture;
}
