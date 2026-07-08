---
outline: deep
---

# RedisTaggedCache

`Raxos\Cache\Redis\RedisTaggedCache` is the tag scoped decorator returned by [`RedisCache::tags()`](/cache/api/RedisCache#tags). It is a readonly class that wraps a `RedisCacheInterface` and a non empty array of tags, and it invalidates groups of related keys together. See [tagged caching](/cache/tagged-cache) for the concept.

```php
readonly class RedisTaggedCache implements RedisTaggedCacheInterface
```

## Construction

### __construct

```php
public function __construct(
    RedisCacheInterface $redis,
    array $tags
)
```

Builds the tag scope from the given cache and tags. Throws a `RedisCommandFailedException` when the tag list is empty. The combined scope is exposed through the public readonly `$scope` property.

## Methods

### key

```php
public function key(string $key): string
```

Returns the given key namespaced with the tag scope hash.

### keyRaw

```php
public function keyRaw(string ...$parts): string
```

Builds a raw key from the cache prefix and the given parts, joined with colons. Used internally to construct both value keys and tag set keys.

### get

```php
public function get(string $key): mixed
```

Reads the value of a key within this tag scope.

### set

```php
public function set(string $key, mixed $value, int $ttl): bool
```

Stores a value, links the key to each tag's member set and extends the tag expiration to at least `$ttl` seconds.

### remember

```php
public function remember(string $key, int $ttl, callable $fn): mixed
```

Returns the cached value when the key exists, otherwise computes it with `$fn`, stores it with the given time to live and returns it.

### exists

```php
public function exists(string $key): bool
```

Checks whether the given key exists within this tag scope.

### del

```php
public function del(string ...$keys): bool
```

Deletes the given keys within this tag scope.

### flush

```php
public function flush(): void
```

Removes every key ever linked to any of the tags, along with the tag sets themselves.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;

$cache = new RedisCache(prefix: 'app:');
$products = $cache->tags(['products', 'catalog']);

$products->set('product:1', 'Widget', 300);
$products->set('product:2', 'Gadget', 300);

// Later, invalidate every key tagged with either tag.
$products->flush();
```
