---
outline: deep
---

# Singleton, Stopwatch and global functions

This page covers three small but widely used pieces of Foundation: the `Singleton` registry, the `Stopwatch` timing helper, and the global functions defined in the `Raxos\Foundation` namespace.

## Singleton

`Raxos\Foundation\Util\Singleton` is a static registry that keeps at most one instance per class name. It is intentionally minimal: there is no dependency injection, only a shared map of class name to instance.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\Singleton;

$service = Singleton::get(MyService::class);     // create on first call, reuse after
Singleton::has(MyService::class);                // true
Singleton::getOrNull(MyService::class);          // instance or null

// force a fresh instance with a plain constructor call
$fresh = Singleton::make(MyService::class);

// create with a factory when the constructor needs arguments
$service = Singleton::register(MyService::class, static fn() => new MyService($config));
```

- `get()` returns the existing instance or creates one with a plain `new $class()` call.
- `getOrNull()` returns the existing instance or `null` if none exists yet.
- `has()` reports whether an instance exists.
- `make()` always creates and stores a new instance.
- `register()` returns the existing instance or creates one using the given factory callable.

### The singleton() function

The global `singleton()` function is a shorthand over the class. It returns an existing instance, or creates one with `make()` when no factory is given, or with `register()` when a factory is supplied. The [Option type](/foundation/option) uses it internally so that `Option::none()` always returns the same shared `None`.

```php
<?php
declare(strict_types=1);

use function Raxos\Foundation\singleton;

$service = singleton(MyService::class);
$service = singleton(MyService::class, static fn() => new MyService($config));
```

## Stopwatch

`Raxos\Foundation\Util\Stopwatch` is a high resolution timer built on `hrtime`. It measures the time between a `start()` and a `stop()`, and reports the result in a chosen unit.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\Stopwatch;
use Raxos\Foundation\Util\StopwatchUnit;

$stopwatch = new Stopwatch('Import');
$stopwatch->start();
// ... work ...
$stopwatch->stop();

$stopwatch->as(StopwatchUnit::MILLISECONDS);      // float, or null when not stopped
$stopwatch->format(StopwatchUnit::MILLISECONDS);  // e.g. '12.5ms'
```

`run()` wraps a callable with a start and stop and returns the callable's result:

```php
$result = $stopwatch->run(static fn() => importEverything());
```

The static `Stopwatch::measure()` runs a callable, writes the elapsed time into a reference parameter, and returns the callable's result:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\Stopwatch;
use Raxos\Foundation\Util\StopwatchUnit;

$elapsed = 0.0;
$result = Stopwatch::measure($elapsed, static fn() => compute(), StopwatchUnit::MILLISECONDS);
// $elapsed now holds the running time in milliseconds
```

### Supporting enums

- `StopwatchState` has the cases `IDLE`, `RUNNING` and `STOPPED`.
- `StopwatchUnit` has the cases `NANOSECONDS`, `MICROSECONDS`, `MILLISECONDS` and `SECONDS`.

See the [Stopwatch API reference](/foundation/api/Stopwatch) for full method signatures.

## Global functions

The `Raxos\Foundation` namespace defines a handful of process and environment helpers, autoloaded through Composer.

```php
<?php
declare(strict_types=1);

use function Raxos\Foundation\{env, isBuiltInServer, isCommandLineInterface, isTesting};

env('APP_NAME', 'Raxos');   // read an environment variable with a default
env('DEBUG', false);        // when the default is a bool, the value is coerced to bool
env('WORKERS', 4);          // when the default is an int, the value is coerced to int

isCommandLineInterface();   // true under the cli SAPI
isBuiltInServer();          // true under the cli-server SAPI
isTesting();                // reads the TESTING environment variable
```

`env()` returns the default when the variable is unset. When the default is an `int` the result is cast to an integer, and when it is a `bool` the values `1`, `true`, `yes` and `on` are treated as true.
