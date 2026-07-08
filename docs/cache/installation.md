---
outline: deep
---

# Installation

Install Cache with Composer.

```shell
composer require raxos/cache
```

## Requirements

- PHP 8.5 or newer.
- The following PHP extensions:
    - `ext-redis`

`RedisCache` throws a `RedisImplementationMissingException` when the `Redis` class is not available on the system, so make sure the extension is loaded before you construct a client.

## Raxos dependencies

Cache builds on a few other Raxos packages:

- [raxos/contract](/contract/): defines the public interfaces (`RedisCacheInterface`, `RedisTaggedCacheInterface` and `RedisCacheExceptionInterface`).
- [raxos/error](/error/): provides the base `Exception` class that every cache exception extends.
- [raxos/foundation](/foundation/): shared utilities used across the Raxos ecosystem.

Return to the [Cache introduction](/cache/).
