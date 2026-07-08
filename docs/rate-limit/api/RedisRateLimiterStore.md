---
outline: deep
---

# RedisRateLimiterStore

`Raxos\RateLimit\Store\RedisRateLimiterStore`

A Redis backed implementation of `RateLimiterStoreInterface` (from [raxos/contract](/contract/)). It
persists operation counts in Redis through the `RedisCache` or `RedisTaggedCache` clients from
[raxos/cache](/cache/), and increments counts atomically using a small Lua script.

```php
final readonly class RedisRateLimiterStore implements RateLimiterStoreInterface
{
    public function __construct(
        RedisCache|RedisTaggedCache $redis,
        string $keyBase = 'ratelimit:'
    );

    public function getOperations(string $key): int;
    public function getTTL(string $key): int;
    public function updateOperations(string $key, int $interval): int;
}
```

## Constructor

### `__construct(RedisCache|RedisTaggedCache $redis, string $keyBase = 'ratelimit:')`

Creates a store on top of a raxos/cache `RedisCache` or `RedisTaggedCache` instance. The `keyBase`
prefix is prepended to every key, so counts from different subsystems can share one Redis instance
without colliding.

## Methods

### `getOperations(string $key): int`

Returns the current operation count for the key without changing it. Returns 0 when the key does not
exist.

### `getTTL(string $key): int`

Returns the remaining time to live for the key, in whole seconds. Never returns a negative value.

### `updateOperations(string $key, int $interval): int`

Atomically increments the operation count and, on the first operation, sets the key to expire after
`interval` seconds. Returns the new count. The increment and expiry are performed together in a Lua
script so concurrent requests cannot lose the expiry.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\RateLimit\{Rate, RateLimiter};
use Raxos\RateLimit\Store\RedisRateLimiterStore;

$store = new RedisRateLimiterStore(new RedisCache(/* ... */), keyBase: 'app:ratelimit:');
$limiter = new RateLimiter(Rate::minute(60), $store);

$limiter->checkLimited('api:user-42');
```

## See also

- [Rate limiting core](/rate-limit/rate-limiting)
- [RateLimiter](/rate-limit/api/RateLimiter)
