---
outline: deep
---

# Stopwatch

`Raxos\Foundation\Util\Stopwatch` is a simple high resolution timer for measuring elapsed time between a start and a stop, built on `hrtime`.

See the [Stopwatch concept page](/foundation/singleton-and-stopwatch) for a guided introduction.

## Signature

```php
namespace Raxos\Foundation\Util;

final class Stopwatch
{
    public private(set) StopwatchState $state = StopwatchState::IDLE;
    public private(set) float $startTime = 0.0;
    public private(set) float $stopTime = 0.0;

    public function __construct(
        public readonly string $description = 'Stopwatch'
    ) {}
}
```

## Methods

```php
public function __construct(string $description = 'Stopwatch')
```
Creates a stopwatch with an optional description.

```php
public function start(): void
```
Starts timing using `hrtime`.

```php
public function stop(): void
```
Stops timing.

```php
public function run(callable $fn): mixed
```
Starts, runs the callable, stops, and returns its result.

```php
public function as(StopwatchUnit $unit): ?float
```
Returns the elapsed time in the given unit, or `null` if the stopwatch is not stopped.

```php
public function format(StopwatchUnit $unit = StopwatchUnit::NANOSECONDS): string
```
Formats the elapsed time with a unit suffix, or returns `-` when not stopped.

```php
public static function measure(float &$runningTime, callable $fn, StopwatchUnit $unit, ?string $description = null): mixed
```
Runs a callable, writes the elapsed time into the reference parameter, and returns the callable's result.

## Enums

```php
enum StopwatchState
{
    case IDLE;
    case RUNNING;
    case STOPPED;
}

enum StopwatchUnit
{
    case NANOSECONDS;
    case MICROSECONDS;
    case MILLISECONDS;
    case SECONDS;
}
```

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\Stopwatch;
use Raxos\Foundation\Util\StopwatchUnit;

$stopwatch = new Stopwatch('Render');
$html = $stopwatch->run(static fn() => renderTemplate());

echo $stopwatch->format(StopwatchUnit::MILLISECONDS);
```
