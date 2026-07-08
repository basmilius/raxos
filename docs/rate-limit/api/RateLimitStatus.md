---
outline: deep
---

# RateLimitStatus

`Raxos\RateLimit\RateLimitStatus`

An immutable snapshot of a single rate limit check. It is the return type of
[`RateLimiter::getStatus`](/rate-limit/api/RateLimiter) and reports how many operations have been
recorded, which [`Rate`](/rate-limit/api/Rate) applies, how long until the window resets, and
whether the limit is exceeded.

```php
final readonly class RateLimitStatus
{
    public bool $exceeded;

    public function __construct(int $operations, Rate $rate, int $ttl);
}
```

## Constructor

### `__construct(int $operations, Rate $rate, int $ttl)`

Creates a status. The `exceeded` property is computed in the constructor as `operations` greater
than the rate quota.

## Properties

| Property | Type | Description |
| --- | --- | --- |
| `operations` | `int` | The number of operations recorded so far in the current window. |
| `rate` | `Rate` | The rate that applies to this check. |
| `ttl` | `int` | The remaining time to live in whole seconds until the window resets. |
| `exceeded` | `bool` | True when `operations` is greater than `rate->quota`. |

## Example

```php
<?php
declare(strict_types=1);

$status = $limiter->getStatus('api:user-42');

if ($status->exceeded) {
    $retryAfter = $status->ttl;
    $remaining = max(0, $status->rate->quota - $status->operations);
}
```

## See also

- [Rate](/rate-limit/api/Rate)
- [RateLimiter](/rate-limit/api/RateLimiter)
