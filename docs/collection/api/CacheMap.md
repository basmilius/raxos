---
outline: deep
---

# CacheMap

`Raxos\Collection\CacheMap` is a `final` [`Map`](/collection/api/Map) specialization that memoizes the result of a callable per key.

```php
final class CacheMap extends Map
```

## Methods

### remember

```php
public function remember(string $key, callable $fn): mixed
```

Returns the value at the key if it is already present. Otherwise it calls the callable, stores its result under the key, and returns it. The callable therefore runs at most once per key.

`CacheMap` inherits every method from `Map`, so `get`, `set`, `has`, `unset`, `merge` and `toArray` all remain available.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\CacheMap;

$settings = new CacheMap();

$value = $settings->remember('theme', static function (): string {
    // Expensive lookup, runs only once for 'theme'.
    return loadThemeFromDisk();
});

// Subsequent calls return the stored value without calling the closure.
$again = $settings->remember('theme', static fn(): string => loadThemeFromDisk());
```

See also the [maps](/collection/maps) concept page.
