---
outline: deep
---

# ArrayList

`Raxos\Collection\ArrayList` is the default mutable, ordered list. It combines the `ArrayListable` operations with array access, iteration, counting and JSON serialization. Most other list types in the package extend it.

```php
class ArrayList implements
    ArrayListInterface,
    MutableArrayListInterface,
    DebuggableInterface,
    JsonSerializable,
    SerializableInterface
```

## Construction

### __construct

```php
public function __construct(array $data = [])
```

Creates a list from a plain PHP array.

### of

```php
public static function of(iterable $items): static
```

Creates a new instance from any iterable. `Traversable` sources are converted with `iterator_to_array`, list arrays are reindexed, and on validated subclasses every item is checked with `validateItem`.

## Mutating operations

### append

```php
public function append(mixed $item): void
```

Adds an item to the end of the list.

### prepend

```php
public function prepend(mixed $item): void
```

Adds an item to the start of the list.

### pop

```php
public function pop(): mixed
```

Removes and returns the last item.

### shift

```php
public function shift(): mixed
```

Removes and returns the first item.

## Transformation operations

These come from the `ArrayListable` trait. Each returns a new instance of the same class and leaves the receiver untouched.

### map

```php
public function map(callable $fn): static
```

Maps every item to a new value.

### filter

```php
public function filter(callable $predicate): static
```

Keeps only items for which the predicate returns true, then reindexes.

### reduce

```php
public function reduce(callable $fn, mixed $initial = null): mixed
```

Reduces the list to a single value.

### first

```php
public function first(?callable $predicate = null, mixed $default = null): mixed
```

Returns the first item, optionally the first matching a predicate, or the default.

### last

```php
public function last(?callable $predicate = null, mixed $default = null): mixed
```

Returns the last item, optionally the last matching a predicate, or the default.

### chunk

```php
public function chunk(int $size): static
```

Splits the list into a list of lists of the given size.

### groupBy

```php
public function groupBy(callable $fn): static
```

Groups items into sublists keyed by the result of the callback.

### sort

```php
public function sort(callable $compare): static
```

Returns a sorted copy using the comparator.

### slice

```php
public function slice(int $offset, ?int $length = null): static
```

Returns a portion of the list.

### convertTo

```php
public function convertTo(string $implementation): ArrayListInterface
```

Rebuilds the list as another `ArrayListInterface` implementation, for example `StringArrayList`.

## Iteration and key/value helpers

### each

```php
public function each(callable $fn): static
```

Runs the callback for every item and key purely for side effects and returns the same instance, unlike `map`.

### keys

```php
public function keys(): static
```

Returns a new list of the current keys.

### values

```php
public function values(): static
```

Returns a new list of the current values, dropping the keys.

### firstKey

```php
public function firstKey(): string|int|null
```

Returns the key of the first item, or `null` when the list is empty.

### lastKey

```php
public function lastKey(): string|int|null
```

Returns the key of the last item, or `null` when the list is empty.

## Reshaping helpers

### column

```php
public function column(string|int ...$columns): static
```

Extracts a single field from every array or object item. Extra columns drill one level deeper into the previous result.

### collapse

```php
public function collapse(): static
```

Flattens one level of nested arrays or nested list items into a single flat list.

### only

```php
public function only(array $keys): static
```

Reduces every item to the given keys: array items are narrowed, objects that define their own `only` method are delegated to, and other items pass through unchanged.

### clone

```php
public function clone(): static
```

Returns a fresh instance of the same class holding the same data.

The trait also provides `every`, `some`, `contains`, `search`, `merge`, `diff`, `unique`, `reverse`, `shuffle`, `splice`, `isEmpty` and `isNotEmpty`.

## Access and serialization

`toArray()` returns the underlying array, `count()` returns the item count, `getIterator()` yields an `ArrayIterator`, and `jsonSerialize()` returns the data for `json_encode`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ArrayList;

$orders = ArrayList::of([
    ['id' => 1, 'total' => 40],
    ['id' => 2, 'total' => 15],
    ['id' => 3, 'total' => 90],
]);

$largeTotals = $orders
    ->filter(static fn(array $order): bool => $order['total'] >= 40)
    ->map(static fn(array $order): int => $order['total']);

$largeTotals->reduce(static fn(int $carry, int $total): int => $carry + $total, 0); // 130
```

See also the [array lists](/collection/array-lists) concept page.
