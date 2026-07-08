---
outline: deep

cards:
    highlights:
        -   title: Rate
            code: true
            details: 'An immutable quota of operations per interval, with second, minute, hour and day constructors.'
            link: /rate-limit/api/Rate
        -   title: RateLimiter
            code: true
            details: 'Ties a Rate to a store and checks or reports a key''s usage.'
            link: /rate-limit/api/RateLimiter
        -   title: RateLimited
            code: true
            details: 'A ready to use raxos/router middleware that adds ratelimit response headers.'
            link: /rate-limit/api/RateLimited
        -   title: RedisRateLimiterStore
            code: true
            details: 'A Redis backed store that persists operation counts through raxos/cache.'
            link: /rate-limit/api/RedisRateLimiterStore
---

# Rate Limit

Redis backed rate limiting with a ready made router middleware. Rate Limit gives Raxos
applications a small, composable layer to cap how often an operation may run for a given key,
whether that key is an IP address, a user id or an API token.

The package has two building blocks. The limiter core (`Rate`, `RateLimiter` and
`RateLimitStatus`) checks and tracks operations against a store. On top of that sits `RateLimited`,
an abstract middleware for [raxos/router](/router/) that enforces a rate on incoming requests and
adds the standard `ratelimit-*` response headers. Operation counts are persisted through a
`RateLimiterStoreInterface`, with a Redis backed implementation provided out of the box.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Rate limiting core](/rate-limit/rate-limiting) explains `Rate`, `RateLimiter` and
  `RateLimitStatus`, and how they combine to decide whether a key exceeded its quota.
- [Router middleware](/rate-limit/router-middleware) shows how to extend `RateLimited` to protect
  your controllers and routes.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\RateLimit\{Rate, RateLimiter};
use Raxos\RateLimit\Store\RedisRateLimiterStore;

$store = new RedisRateLimiterStore(new RedisCache(/* ... */));
$limiter = new RateLimiter(Rate::minutes(15, 10), $store);

$limiter->checkLimited('login:203.0.113.5');
```

`checkLimited` throws a `LimitExceededException` as soon as the key exceeds its quota within the
configured interval.

## Next steps

Head to [installation](/rate-limit/installation) for requirements and dependencies, or use the
sidebar to navigate the package.
