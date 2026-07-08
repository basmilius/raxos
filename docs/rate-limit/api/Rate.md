---
outline: deep
---

# Rate

`Raxos\RateLimit\Rate`

An immutable value object that describes a limit as a quota of operations allowed within a time
interval. The interval is stored in seconds. A set of named constructors covers the common cases so
you rarely need to compute seconds by hand.

```php
final readonly class Rate
{
    public function __construct(int $interval, int $quota);
}
```

Both `interval` and `quota` are exposed as public readonly properties.

## Constructor

### `__construct(int $interval, int $quota)`

Creates a rate for a raw interval in seconds and a quota. Throws `InvalidParameterException` when
the interval or the quota is not greater than 0.

## Named constructors

| Method | Description |
| --- | --- |
| `static second(int $quota): self` | A rate of one second with the given quota. |
| `static seconds(int $n, int $quota): self` | A rate of `n` seconds with the given quota. |
| `static minute(int $quota): self` | A rate of one minute with the given quota. |
| `static minutes(int $n, int $quota): self` | A rate of `n` minutes with the given quota. |
| `static hour(int $quota): self` | A rate of one hour with the given quota. |
| `static hours(int $n, int $quota): self` | A rate of `n` hours with the given quota. |
| `static day(int $quota): self` | A rate of one day with the given quota. |
| `static days(int $days, int $quota): self` | A rate of `n` days with the given quota. |

Each named constructor can throw `InvalidParameterException` when the resulting interval or quota is
not greater than 0.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\RateLimit\Rate;

$rate = Rate::minutes(15, 100); // 100 operations per 15 minutes

$rate->interval; // 900
$rate->quota;    // 100
```

## See also

- [Rate limiting core](/rate-limit/rate-limiting)
- [RateLimiter](/rate-limit/api/RateLimiter)
