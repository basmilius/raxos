---
outline: deep
---

# Maps

A map is a string keyed dictionary. Collection provides three variants: the mutable `Map`, the memoizing `CacheMap`, and the immutable `ReadonlyMap`. All of them implement `Countable`, `IteratorAggregate` and `JsonSerializable`.

## Map

`Map` is the mutable dictionary. It supports `get`, `has`, `set`, `unset` and `merge`, plus `toArray` to get the underlying data back.

```php
<?php
declare(strict_types=1);

use Raxos\Collection\Map;

$config = new Map([
    'host' => 'localhost',
    'port' => 3306,
]);

$config->get('host');            // 'localhost'
$config->get('timeout', 30);     // 30, the default, key is absent
$config->has('port');            // true

$config->set('timeout', 15);
$config->unset('port');

$config->toArray(); // ['host' => 'localhost', 'timeout' => 15]
```

`merge()` mutates the map in place and returns it, so it stays chainable. It accepts either another map or a plain array.

```php
$defaults = new Map(['debug' => false, 'cache' => true]);
$defaults->merge(['debug' => true]);

$defaults->toArray(); // ['debug' => false, 'cache' => true]
```

::: info
`Map::merge()` uses the union operator, so keys already present keep their existing value. In the example above `debug` stays `false`.
:::

## CacheMap

`CacheMap` extends `Map` and adds `remember()`, which memoizes the result of a callable per key. The callable runs only the first time a key is requested; afterwards the stored value is returned.

```php
<?php
declare(strict_types=1);

use Raxos\Collection\CacheMap;

$cache = new CacheMap();

$user = $cache->remember('user:1', static function () {
    // Only runs once for this key.
    return loadUserFromDatabase(1);
});

$again = $cache->remember('user:1', static fn() => loadUserFromDatabase(1));
// $again is the stored value, the callable is not called again.
```

## ReadonlyMap

`ReadonlyMap` is the immutable variant. It has no `set` or `unset`, and its `get()` takes no default (it returns `null` for missing keys). `merge()` returns a new instance instead of mutating in place.

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ReadonlyMap;

$base = new ReadonlyMap(['a' => 1, 'b' => 2]);

$base->get('a');   // 1
$base->get('z');   // null
$base->has('b');   // true

$extended = $base->merge(['c' => 3]);
// $base is unchanged, $extended is a new ReadonlyMap with a, b and c.
```

Unlike `Map::merge()`, `ReadonlyMap::merge()` uses `array_merge`, so keys in the incoming data overwrite existing keys.
