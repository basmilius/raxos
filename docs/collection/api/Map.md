---
outline: deep
---

# Map

`Raxos\Collection\Map` is a mutable, string keyed dictionary.

```php
class Map implements
    DebuggableInterface,
    MapInterface,
    MutableMapInterface,
    JsonSerializable,
    SerializableInterface
```

## Construction

### __construct

```php
public function __construct(array $data = [])
```

Creates a map from a plain associative array.

## Methods

### get

```php
public function get(string $key, mixed $default = null): mixed
```

Returns the value at the key, or the default when the key is absent.

### has

```php
public function has(string $key): bool
```

Returns whether a value exists at the key.

### set

```php
public function set(string $key, mixed $value): void
```

Sets the value at the key.

### unset

```php
public function unset(string $key): void
```

Removes the value at the key.

### merge

```php
public function merge(MapInterface|array $other): static
```

Merges another map or array into this one in place and returns it. Keys already present keep their existing value.

### toArray

```php
public function toArray(): array
```

Returns the underlying data as a plain array.

## Access and serialization

`Map` implements `Countable`, `IteratorAggregate` and `JsonSerializable`, so `count()`, `foreach` and `json_encode` all work directly.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\Map;

$headers = new Map([
    'content-type' => 'application/json',
]);

$headers->set('x-request-id', 'abc-123');
$headers->has('content-type');           // true
$headers->get('accept', '*/*');          // '*/*'

$headers->toArray();
// ['content-type' => 'application/json', 'x-request-id' => 'abc-123']
```

See also the [maps](/collection/maps) concept page.
