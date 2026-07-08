---
outline: deep
---

# Rate limiting core

The core of the package is made up of four small pieces: a `Rate` value object, a store abstraction
(`RateLimiterStoreInterface`), the `RateLimiter` that ties them together, and the `RateLimitStatus`
snapshot it returns. Together they answer one question for a given key: has this key exceeded its
allowed number of operations within the current interval?

## Rate

A `Rate` is an immutable value object describing a quota of operations over an interval expressed in
seconds. You can build one directly or, more commonly, through one of its named constructors.

```php
<?php
declare(strict_types=1);

use Raxos\RateLimit\Rate;

$perMinute = Rate::minute(60);        // 60 operations per minute
$perQuarter = Rate::minutes(15, 100); // 100 operations per 15 minutes
$perHour = Rate::hour(1000);          // 1000 operations per hour
$perDay = Rate::day(10000);           // 10000 operations per day

$raw = new Rate(30, 5);               // 5 operations per 30 seconds
```

The available named constructors are `second`, `seconds`, `minute`, `minutes`, `hour`, `hours`,
`day` and `days`. Both the `interval` and the `quota` must be greater than 0. Constructing a rate
with an invalid value throws an `InvalidParameterException`.

```php
<?php
declare(strict_types=1);

use Raxos\RateLimit\Rate;
use Raxos\RateLimit\Error\InvalidParameterException;

try {
    $rate = Rate::minute(0);
} catch (InvalidParameterException $err) {
    // Quota must be greater than 0.
}
```

## The store interface

Where operation counts are kept is abstracted behind `RateLimiterStoreInterface` (defined in
[raxos/contract](/contract/)). A store exposes three operations: reading the current count for a
key, reading its remaining time to live, and atomically incrementing the count. The package ships a
Redis backed implementation, `RedisRateLimiterStore`, but any implementation of the interface works.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\RateLimit\Store\RedisRateLimiterStore;

$store = new RedisRateLimiterStore(new RedisCache(/* ... */));
```

## RateLimiter

A `RateLimiter` combines a `Rate` with a store. It exposes two methods, both keyed by a string you
choose to identify the caller.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\RateLimit\{Rate, RateLimiter};
use Raxos\RateLimit\Store\RedisRateLimiterStore;

$store = new RedisRateLimiterStore(new RedisCache(/* ... */));
$limiter = new RateLimiter(Rate::minute(60), $store);

$status = $limiter->getStatus('api:user-42');

if ($status->exceeded) {
    // Serve a 429 response, for example.
}
```

`getStatus` increments the operation count by default and returns a `RateLimitStatus`. Pass
`increment: false` to inspect the current usage without counting the call, which is useful for
read-only checks.

```php
$peek = $limiter->getStatus('api:user-42', increment: false);
```

`checkLimited` is a convenience wrapper around `getStatus`. It throws a `LimitExceededException`
when the key has exceeded its quota, and returns nothing otherwise.

```php
<?php
declare(strict_types=1);

use Raxos\RateLimit\Error\LimitExceededException;

try {
    $limiter->checkLimited('api:user-42');
    // Proceed with the operation.
} catch (LimitExceededException $err) {
    // The caller is over the limit.
}
```

## RateLimitStatus

Both methods work with `RateLimitStatus`, an immutable snapshot of a single check. It carries the
number of operations recorded so far, the `Rate` that applies, the remaining time to live in whole
seconds, and a computed `exceeded` flag that is true when `operations` is greater than the rate
quota.

```php
$status = $limiter->getStatus('api:user-42');

$status->operations; // int, operations counted so far
$status->rate;       // Rate, the applicable rate
$status->ttl;        // int, seconds until the window resets
$status->exceeded;   // bool, operations > rate quota
```

These four properties are exactly what the [router middleware](/rate-limit/router-middleware) uses
to build the `ratelimit-*` response headers.

## Error handling

Both exceptions live in the `Raxos\RateLimit\Error` namespace and implement
`RateLimitExceptionInterface` from [raxos/contract](/contract/):

- `InvalidParameterException` is thrown when a `Rate` is constructed with a non-positive interval or
  quota.
- `LimitExceededException` is thrown by `checkLimited` when a key is over its limit.
