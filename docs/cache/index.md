---
outline: deep

cards:
    highlights:
        -   title: RedisCache
            code: true
            details: 'A typed Redis client with grouped commands for strings, sets, keys, pub/sub and server operations.'
            link: /cache/api/RedisCache
        -   title: RedisTaggedCache
            code: true
            details: 'Tag based invalidation that flushes a group of related keys together without scanning the keyspace.'
            link: /cache/api/RedisTaggedCache
        -   title: RedisUtil
            code: true
            details: 'Wraps every Redis call so a RedisException becomes a typed Raxos exception.'
            link: /cache/api/RedisUtil
---

# Cache

Raxos Cache wraps the PHP Redis extension in a small, typed client. `RedisCache` exposes connection management plus grouped command traits for strings, sets, keys, pub/sub and server operations, all wrapped so that a `RedisException` is converted into a typed Raxos exception. `RedisTaggedCache` layers tag based invalidation on top, so a group of keys can be flushed together without scanning the whole keyspace.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Basic usage](/cache/basic-usage): construct a `RedisCache`, manage the connection, select a database and use the `remember()` pattern for compute once, cache once workflows.
- [Command groups](/cache/command-groups): the trait based organization of Redis commands available on `RedisCache`, covering strings, sets, keys, pub/sub and server administration.
- [Tagged caching](/cache/tagged-cache): how `RedisTaggedCache` scopes keys to a tag set and flushes every related key in one call.
- [Error handling](/cache/error-handling): the exception hierarchy that surfaces Redis failures as typed, catchable Raxos exceptions.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;

$cache = new RedisCache(prefix: 'app:');

$user = $cache->remember('user:42', 60, static fn(): array => [
    'id' => 42,
    'name' => 'Bas',
]);
```

## Installation

Install the package with Composer. See [installation](/cache/installation) for the required PHP version and extensions.

```shell
composer require raxos/cache
```
