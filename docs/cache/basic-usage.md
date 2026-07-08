---
outline: deep
---

# Basic usage

`RedisCache` is a thin, typed wrapper around the PHP Redis extension. This page covers how to construct a client, manage the connection, select a database and cache computed values.

## Constructing a client

The constructor takes a key prefix, a host, a port and a timeout. By default it connects to the server immediately.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;

$cache = new RedisCache(
    prefix: 'app:',
    host: '127.0.0.1',
    port: 6379,
    timeout: 0.0
);
```

The `prefix` is exposed through `getPrefix()` and is used by the tagged cache to namespace its keys. Pass `connect: false` to build the client without opening the connection right away, for example when you want to configure the underlying `Redis` object in a subclass first.

```php
$cache = new RedisCache(prefix: 'app:', connect: false);

// Later, when you are ready.
$cache->connect();
```

::: info
The constructor throws a `RedisImplementationMissingException` when the `Redis` class is not available, and a `RedisConnectionFailedException` when the initial connection fails. See [error handling](/cache/error-handling) for the full hierarchy.
:::

## Managing the connection

`isConnected()` reports whether the client currently has an open connection, and `connect()` opens it.

```php
if (!$cache->isConnected()) {
    $cache->connect();
}
```

## Selecting a database

Redis servers expose several logical databases addressed by index. Use `selectDatabase()` to switch the active one.

```php
$cache->selectDatabase(0);
```

If the server refuses the selection, a `RedisCommandFailedException` is thrown.

## Remembering computed values

`remember()` is the compute once, cache once pattern. It returns the cached value when the key exists, otherwise it runs the callback, stores the result with `setex()` for the given time to live in seconds and returns it.

```php
$user = $cache->remember('user:42', 60, static function (): array {
    // Expensive lookup, only runs when the key is missing.
    return ['id' => 42, 'name' => 'Bas'];
});
```

## Customizing serialization

Redis stores strings. When you want to cache non scalar values such as arrays or objects, subclassing `RedisCache` is a common way to add serialization around the string commands.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use function json_decode;
use function json_encode;

final class JsonRedisCache extends RedisCache
{
    public function get(string $key): mixed
    {
        $value = parent::get($key);

        return $value !== false ? json_decode($value, true) : null;
    }

    public function setex(string $key, mixed $value, int $ttl): bool
    {
        return parent::setex($key, json_encode($value), $ttl);
    }
}
```

Because `remember()` calls `exists()`, `get()` and `setex()` internally, overriding those string commands is enough to make the caching helpers work with structured data.
