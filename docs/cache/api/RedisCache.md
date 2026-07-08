---
outline: deep
---

# RedisCache

`Raxos\Cache\Redis\RedisCache` is the main cache client. It combines connection management with the grouped command traits, so a single instance exposes the string, set, key, pub/sub and server commands documented in [command groups](/cache/command-groups).

```php
class RedisCache implements RedisCacheInterface
{
    use RedisKeys;
    use RedisPubSub;
    use RedisServer;
    use RedisSets;
    use RedisStrings;
}
```

## Construction

### __construct

```php
public function __construct(
    string $prefix,
    string $host = '127.0.0.1',
    int $port = 6379,
    float $timeout = 0.0,
    bool $connect = true
)
```

Creates the underlying `Redis` object and connects unless `$connect` is `false`. Throws a `RedisImplementationMissingException` when the `Redis` class is missing, and a `RedisConnectionFailedException` when the initial connection fails. The `$prefix`, `$host`, `$port` and `$timeout` are exposed as public readonly properties.

## Methods

### connect

```php
public function connect(): bool
```

Opens the connection to the configured Redis server.

### getPrefix

```php
public final function getPrefix(): string
```

Returns the configured key prefix.

### isConnected

```php
public final function isConnected(): bool
```

Returns whether the client currently has an open connection.

### remember

```php
public function remember(string $key, int $ttl, callable $fn): mixed
```

Returns the cached value when the key exists, otherwise computes it with `$fn`, stores it with `setex()` for `$ttl` seconds and returns it.

### selectDatabase

```php
public function selectDatabase(int $databaseId): void
```

Switches the active Redis logical database. Throws a `RedisCommandFailedException` when the selection fails.

### tags

```php
public function tags(array $tags): RedisTaggedCacheInterface
```

Returns a [RedisTaggedCache](/cache/api/RedisTaggedCache) scoped to the given tags.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;

$cache = new RedisCache(prefix: 'app:', host: '127.0.0.1', port: 6379);

$cache->selectDatabase(0);

$user = $cache->remember('user:42', 60, static fn(): array => [
    'id' => 42,
    'name' => 'Bas',
]);
```
