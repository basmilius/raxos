---
outline: deep
---

# Command groups

`RedisCache` does not implement Redis commands directly. Instead the commands are split into focused traits that the class composes together. Every method mirrors the matching Redis command and routes the call through `RedisUtil::wrap()`, so any `RedisException` becomes a `RedisCacheExceptionInterface` (see [error handling](/cache/error-handling)).

```php
class RedisCache implements RedisCacheInterface
{
    use RedisKeys;
    use RedisPubSub;
    use RedisServer;
    use RedisSets;
    use RedisStrings;

    // ...
}
```

Because these are all combined into `RedisCache`, every command below is available directly on your cache instance.

## Strings

`RedisStrings` covers the string value commands, including counters and bit operations.

```php
$cache->set('greeting', 'hello');
$value = $cache->get('greeting');

$cache->setex('session:1', 'token', 3600);
$cache->setnx('lock:1', '1');

$views = $cache->incr('post:1:views');
$cache->decr('stock:42');

$values = $cache->mget('a', 'b', 'c');
$cache->mset(['a' => '1', 'b' => '2']);
```

Available methods: `append()`, `bitcount()`, `bitop()`, `bitpos()`, `decr()`, `decrby()`, `get()`, `getbit()`, `getrange()`, `getset()`, `incr()`, `incrby()`, `incrbyfloat()`, `mget()`, `mset()`, `msetnx()`, `psetex()`, `set()`, `setbit()`, `setex()`, `setnx()`, `setrange()` and `strlen()`.

::: tip
`setex()` and `psetex()` take the arguments in `(key, value, ttl)` order, which differs from the native Redis extension where the ttl comes second. The wrapper reorders them for you.
:::

## Sets

`RedisSets` covers unordered set commands, including the union, intersection and difference operations.

```php
$cache->sadd('tags:1', 'php', 'redis');
$members = $cache->smembers('tags:1');

if ($cache->sismember('tags:1', 'php')) {
    // ...
}

$shared = $cache->sinter('tags:1', 'tags:2');
$all = $cache->sunion('tags:1', 'tags:2');
$only = $cache->sdiff('tags:1', 'tags:2');
```

Available methods: `sadd()`, `scard()`, `sdiff()`, `sdiffstore()`, `sinter()`, `sinterstore()`, `sismember()`, `smembers()`, `smove()`, `spop()`, `srandmember()`, `srem()`, `sunion()` and `sunionstore()`.

## Keys

`RedisKeys` covers key management: existence, expiration, renaming and deletion.

```php
if ($cache->exists('user:42')) {
    $cache->expire('user:42', 120);
}

$ttl = $cache->ttl('user:42');
$cache->persist('user:42');

$cache->rename('user:42', 'user:43');
$cache->del('user:43');
$cache->touch('a', 'b');

$keys = $cache->keys('user:*');
```

Available methods: `del()`, `dump()`, `exists()`, `expire()`, `expireAt()`, `keys()`, `migrate()`, `move()`, `object()`, `persist()`, `pexpire()`, `pexpireat()`, `pttl()`, `randomkey()`, `rename()`, `renamenx()`, `restore()`, `sort()`, `touch()`, `ttl()`, `type()`, `unlink()` and `wait()`.

## Pub/sub

`RedisPubSub` covers publish and subscribe messaging.

```php
$cache->publish('events', 'user.updated');

$cache->subscribe(['events'], static function (mixed $redis, string $channel, string $message): void {
    // Handle the incoming message.
});
```

Available methods: `subscribe()`, `psubscribe()`, `unsubscribe()`, `punsubscribe()`, `publish()` and `pubsub()`.

::: warning
`subscribe()` and `psubscribe()` block the current process while they wait for messages, which matches the behavior of the underlying Redis extension. Run them in a dedicated worker rather than in a request cycle.
:::

## Server

`RedisServer` covers administrative commands and Lua script evaluation.

```php
$cache->flushDatabase();
$cache->flushAll();

$result = $cache->eval(
    'return redis.call("GET", KEYS[1])',
    ['user:42']
);
```

Available methods: `flushAll()`, `flushDatabase()` and `eval()`. The `eval()` method takes the script, an array of keys and an array of arguments, and forwards them to the server in the format the extension expects.
