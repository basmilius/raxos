---
outline: deep
---

# ReadonlyArrayList

`Raxos\Collection\ReadonlyArrayList` is the immutable counterpart of [`ArrayList`](/collection/api/ArrayList). It exposes the same read and transformation operations, but any write through array access throws a `CollectionImmutableException`.

```php
readonly class ReadonlyArrayList implements
    ArrayListInterface,
    DebuggableInterface,
    JsonSerializable,
    SerializableInterface
```

## Construction

### __construct

```php
public function __construct(array $data = [])
```

Creates a readonly list from a plain PHP array.

### of

```php
public static function of(iterable $items): static
```

Creates a new instance from any iterable, converting `Traversable` sources and reindexing list arrays.

## Read and transformation operations

`ReadonlyArrayList` uses the same `ArrayListable` trait as `ArrayList`, so `map`, `filter`, `reduce`, `first`, `last`, `chunk`, `groupBy`, `sort`, `slice`, `convertTo` and the rest are all available. Each transformation returns a new `ReadonlyArrayList`; none mutate the receiver.

Reading through array access, `count()`, iteration, `toArray()` and `jsonSerialize()` all work exactly as on `ArrayList`.

## Immutability

`offsetSet` and `offsetUnset` throw a `CollectionImmutableException` because the class is readonly. The append, prepend, pop and shift mutators from `ArrayList` are not part of `ReadonlyArrayList`.

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ReadonlyArrayList;
use Raxos\Collection\Error\CollectionImmutableException;

$list = ReadonlyArrayList::of([1, 2, 3]);

$doubled = $list->map(static fn(int $number): int => $number * 2); // new list

try {
    $list[] = 4; // throws
} catch (CollectionImmutableException $exception) {
    // The collection is immutable.
}
```

See also the [array lists](/collection/array-lists) concept page.
