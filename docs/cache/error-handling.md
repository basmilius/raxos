---
outline: deep
---

# Error handling

Every public method on the cache runs its underlying Redis call through `RedisUtil::wrap()`. That wrapper catches the extension's `RedisException` and rethrows it as a typed Raxos exception, so callers never have to deal with the raw extension errors.

## The wrapper

`RedisUtil::wrap()` invokes a callable and converts any `RedisException` into a `RedisErrorException`.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisUtil;

$value = RedisUtil::wrap($connection->get(...), 'user:42');
```

## Exception types

All cache exceptions live in `Raxos\Cache\Redis\Error`, extend the base `Exception` from [raxos/error](/error/) and implement `RedisCacheExceptionInterface` from [raxos/contract](/contract/).

| Exception | Thrown when |
| --- | --- |
| `RedisErrorException` | A `RedisException` from the extension is caught during any wrapped call. |
| `RedisConnectionFailedException` | The initial `connect()` call from the constructor fails. |
| `RedisImplementationMissingException` | The `Redis` class is not available on the system. |
| `RedisCommandFailedException` | A command level failure, such as an empty tag list or a database that cannot be selected. |

## Catching failures

Because every one of these exceptions implements `RedisCacheExceptionInterface`, you can catch a single interface to handle any cache failure.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\Contract\Cache\RedisCacheExceptionInterface;

try {
    $cache = new RedisCache(prefix: 'app:');
    $cache->selectDatabase(1);
    $value = $cache->get('user:42');
} catch (RedisCacheExceptionInterface $err) {
    // Every cache failure ends up here.
}
```

When you extend `RedisCache` with additional raw Redis calls, route them through `RedisUtil::wrap()` too so they follow the same error convention.
