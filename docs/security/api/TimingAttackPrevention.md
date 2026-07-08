---
outline: deep
---

# TimingAttackPrevention

`Raxos\Security\TimingAttackPrevention` pads an operation to a fixed minimum duration to reduce the risk of timing based attacks. It uses the `Stopwatch` utility from [raxos/foundation](/foundation/).

```php
final readonly class TimingAttackPrevention
```

## Methods

### `__construct`

```php
public function __construct(private int $milliseconds)
```

Creates an instance targeting the given minimum duration in milliseconds.

### `begin`

```php
public function begin(): void
```

Starts the internal stopwatch.

### `end`

```php
public function end(): void
```

Stops the stopwatch and sleeps for the remaining time, so the whole operation takes at least the configured number of milliseconds.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\TimingAttackPrevention;

$timing = new TimingAttackPrevention(250);
$timing->begin();

// Verify a password or token here.

$timing->end();
```
