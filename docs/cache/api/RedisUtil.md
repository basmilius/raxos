---
outline: deep
---

# RedisUtil

`Raxos\Cache\Redis\RedisUtil` is a small static helper that turns a `RedisException` from the extension into a typed `RedisErrorException`. Every command group trait, as well as `RedisCache` and `RedisTaggedCache`, routes its Redis calls through this helper. See [error handling](/cache/error-handling) for the exception hierarchy.

```php
final class RedisUtil
```

## Methods

### wrap

```php
public static function wrap(callable $fn, mixed ...$arguments): mixed
```

Calls `$fn` with the given arguments and returns its result. When the callable throws a `RedisException`, it is caught and rethrown as a `RedisErrorException`, which implements `RedisCacheExceptionInterface`.

## Example

Use `wrap()` when you extend `RedisCache` with additional raw Redis calls that should follow the same error convention.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\{RedisCache, RedisUtil};

final class ExtendedRedisCache extends RedisCache
{
    public function llen(string $key): int
    {
        return RedisUtil::wrap($this->connection->lLen(...), $key);
    }
}
```
