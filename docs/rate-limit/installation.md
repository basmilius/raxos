---
outline: deep
---

# Installation

Install Rate Limit with Composer.

```shell
composer require raxos/rate-limit
```

## Requirements

- PHP 8.5 or newer.
- A Redis server when using the built in `RedisRateLimiterStore`.

The package declares no PHP extension requirements of its own. Redis access is handled through
[raxos/cache](/cache/), so its requirements apply when you use the Redis backed store.

## Raxos dependencies

Rate Limit builds on a small set of other Raxos packages, which Composer installs for you:

- [raxos/cache](/cache/) provides the `RedisCache` and `RedisTaggedCache` clients used by
  `RedisRateLimiterStore`.
- [raxos/foundation](/foundation/) supplies shared base utilities.
- [raxos/http](/http/) provides the `HttpRequest` and `HttpResponse` objects used by the router
  middleware.
- [raxos/router](/router/) defines the `MiddlewareInterface` that `RateLimited` implements.

The `RateLimiterStoreInterface` and `RateLimitExceptionInterface` types live in
[raxos/contract](/contract/).

Return to the [Rate Limit introduction](/rate-limit/).
