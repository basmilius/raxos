---
outline: deep
---

# RateLimiter

`Raxos\RateLimit\RateLimiter`

Combines a [`Rate`](/rate-limit/api/Rate) with a `RateLimiterStoreInterface` to check and track
operations for a key. It is the main entry point of the [rate limiting core](/rate-limit/rate-limiting).

```php
final readonly class RateLimiter
{
    public function __construct(Rate $rate, RateLimiterStoreInterface $store);

    public function checkLimited(string $key): void;
    public function getStatus(string $key, bool $increment = true): RateLimitStatus;
}
```

Both `rate` and `store` are exposed as public readonly properties.

## Constructor

### `__construct(Rate $rate, RateLimiterStoreInterface $store)`

Creates a limiter for the given rate, backed by the given store. The store
(`RateLimiterStoreInterface` from [raxos/contract](/contract/)) decides where operation counts are
persisted; use [`RedisRateLimiterStore`](/rate-limit/api/RedisRateLimiterStore) for Redis.

## Methods

### `checkLimited(string $key): void`

Throws `LimitExceededException` when the key has exceeded its rate. Returns nothing when the key is
still within its quota. Internally it calls `getStatus`, so this call increments the operation
count.

### `getStatus(string $key, bool $increment = true): RateLimitStatus`

Returns the current [`RateLimitStatus`](/rate-limit/api/RateLimitStatus) for the key. Increments the
operation count unless `increment` is set to `false`, which lets you inspect usage without counting
the call.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\RateLimit\{Rate, RateLimiter};
use Raxos\RateLimit\Store\RedisRateLimiterStore;
use Raxos\RateLimit\Error\LimitExceededException;

$store = new RedisRateLimiterStore(new RedisCache(/* ... */));
$limiter = new RateLimiter(Rate::minute(60), $store);

try {
    $limiter->checkLimited('api:user-42');
} catch (LimitExceededException $err) {
    // The caller is over the limit.
}

$status = $limiter->getStatus('api:user-42', increment: false);
```

## See also

- [Rate](/rate-limit/api/Rate)
- [RateLimitStatus](/rate-limit/api/RateLimitStatus)
- [RedisRateLimiterStore](/rate-limit/api/RedisRateLimiterStore)
