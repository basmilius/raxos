---
outline: deep
---

# Tagged caching

`RedisTaggedCache` layers tag based invalidation on top of a `RedisCache`. Tags let you group related keys so you can flush all of them together, without scanning the whole keyspace with a pattern match.

## Creating a tag scope

Call `tags()` on a `RedisCache` with one or more tag names. It returns a `RedisTaggedCacheInterface` scoped to those tags.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;

$cache = new RedisCache(prefix: 'app:');
$products = $cache->tags(['products', 'catalog']);
```

The tag list must not be empty. Passing an empty array throws a `RedisCommandFailedException`.

## How keys are scoped

Every value written through a tagged cache is stored under a namespaced key. The key is built from the cache prefix and a hash of the combined tag scope, so the same logical key stored under different tag sets never collides.

```php
$products->set('product:1', 'Widget', 300);
$products->set('product:2', 'Gadget', 300);
```

When you call `set()`, the tagged cache also links the key to a set for each tag and extends that tag's expiration to cover the new value. This membership is what makes group invalidation possible.

## Reading and writing

`get()`, `set()`, `exists()`, `del()` and `remember()` work the same way as on `RedisCache`, but they operate within the tag scope.

```php
$name = $products->remember('product:3', 300, static fn(): string => 'Gizmo');

if ($products->exists('product:1')) {
    $value = $products->get('product:1');
}

$products->del('product:2');
```

## Flushing a group

`flush()` removes every key that was ever linked to any of the tags, along with the tag sets themselves.

```php
// Invalidate every key tagged with products or catalog.
$products->flush();
```

Because the tagged cache keeps a set of member keys per tag, a flush only touches the keys that belong to the scope. There is no keyspace scan and no impact on unrelated keys.

::: tip
Tag membership is stored in Redis sets under keys derived from the tag name. When you construct a tagged cache with the same tags again later, it resolves to the same scope, so invalidation still reaches keys written in an earlier request.
:::
