---
outline: deep
---

# ReadonlyMap

`Raxos\Collection\ReadonlyMap` is the immutable dictionary. It has no `set` or `unset`, and `merge` returns a new instance instead of mutating in place.

```php
readonly class ReadonlyMap implements
    DebuggableInterface,
    MapInterface,
    JsonSerializable,
    SerializableInterface
```

## Construction

### __construct

```php
public function __construct(array $data = [])
```

Creates a readonly map from a plain associative array.

## Methods

### get

```php
public function get(string $key): mixed
```

Returns the value at the key, or `null` when the key is absent. Unlike [`Map::get`](/collection/api/Map), it does not take a default argument.

### has

```php
public function has(string $key): bool
```

Returns whether a value exists at the key.

### merge

```php
public function merge(MapInterface|array $other): static
```

Returns a new map with the other map or array merged in. The current map is left unchanged, and incoming keys overwrite existing ones.

### toArray

```php
public function toArray(): array
```

Returns the underlying data as a plain array.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ReadonlyMap;

$defaults = new ReadonlyMap(['locale' => 'en', 'debug' => false]);

$defaults->get('locale'); // 'en'
$defaults->get('missing'); // null

$overridden = $defaults->merge(['debug' => true]);
// $defaults is unchanged; $overridden has debug => true.
```

See also the [maps](/collection/maps) concept page.
