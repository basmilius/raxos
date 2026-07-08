---
outline: deep
---

# Array lists

An array list is an ordered, keyed collection of values. Collection ships two variants: the mutable `ArrayList` and the immutable `ReadonlyArrayList`. Both implement `ArrayAccess`, `Countable`, `IteratorAggregate` and `JsonSerializable`, and both share the same set of read and transformation operations through the `ArrayListable` trait.

## Creating a list

Construct a list directly from a plain array, or use `of()` to build one from any iterable, including another list or a `Traversable`.

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ArrayList;

$fromArray = new ArrayList([1, 2, 3]);
$fromIterable = ArrayList::of(['a', 'b', 'c']);
$fromGenerator = ArrayList::of((static function () {
    yield 1;
    yield 2;
})());
```

When the source is a list (a zero indexed array), `of()` reindexes the values. On typed subclasses it also validates every item, which is covered on the [typed lists](/collection/typed-lists) page.

## Array access and iteration

A list behaves like a native array for reading, counting and iterating.

```php
$list = new ArrayList(['first', 'second', 'third']);

$list[0];            // 'first'
isset($list[5]);     // false
count($list);        // 3

foreach ($list as $index => $value) {
    // 0 => 'first', 1 => 'second', 2 => 'third'
}
```

Because `ArrayList` is mutable, you can also write and unset through array access.

```php
$list[] = 'fourth';
$list[0] = 'zeroth';
unset($list[1]);
```

## Adding and removing items

`ArrayList` exposes stack and queue style operations.

```php
$list = new ArrayList([2, 3]);

$list->append(4);   // [2, 3, 4]
$list->prepend(1);  // [1, 2, 3, 4]

$last = $list->pop();    // 4, list is now [1, 2, 3]
$first = $list->shift(); // 1, list is now [2, 3]
```

## Transforming a list

Transformation operations never mutate the receiver. Each one returns a new instance of the same class, so calls chain together.

```php
$people = ArrayList::of([
    ['name' => 'Alice', 'age' => 30],
    ['name' => 'Bob', 'age' => 17],
    ['name' => 'Carol', 'age' => 42],
]);

$names = $people
    ->filter(static fn(array $person): bool => $person['age'] >= 18)
    ->sort(static fn(array $left, array $right): int => $left['age'] <=> $right['age'])
    ->map(static fn(array $person): string => $person['name']);

$names->toArray(); // ['Alice', 'Carol']
```

Some of the most used operations:

- `map(callable $fn)`, `filter(callable $predicate)` and `reduce(callable $fn, mixed $initial = null)` for the classic functional trio.
- `first()` and `last()`, each optionally taking a predicate and a default.
- `chunk(int $size)` to split into a list of lists, and `groupBy(callable $fn)` to group items by a computed key.
- `sort(callable $compare)`, `reverse()`, `shuffle()` and `unique()` for reordering and deduplication.
- `slice(int $offset, ?int $length = null)`, `splice()`, `merge()` and `diff()` for structural changes.
- `contains()`, `some()`, `every()`, `isEmpty()` and `search()` for inspection.

See the [ArrayList reference](/collection/api/ArrayList) for the complete list.

## Converting between implementations

`convertTo()` rebuilds a list as a different `ArrayListInterface` implementation, for example to turn a plain list of strings into a `StringArrayList` and gain its helpers.

```php
use Raxos\Collection\{ArrayList, StringArrayList};

$labels = new ArrayList(['red', 'green', 'blue']);
$strings = $labels->convertTo(StringArrayList::class);

$strings->join(' / '); // 'red / green / blue'
```

## Immutable lists

`ReadonlyArrayList` offers the same read and transformation operations as `ArrayList`, but any write through array access throws a `CollectionImmutableException`.

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ReadonlyArrayList;
use Raxos\Collection\Error\CollectionImmutableException;

$list = ReadonlyArrayList::of([1, 2, 3]);

$list->map(static fn(int $number): int => $number * 2); // fine, returns a new list

try {
    $list[0] = 99; // throws
} catch (CollectionImmutableException $exception) {
    // The collection is immutable.
}
```

Transformation methods still return new `ReadonlyArrayList` instances, so you keep chaining while the original data stays untouched.

## Inspecting keys and values

`keys()` and `values()` return a new list, of the same class as the receiver, holding respectively the array keys and the array values of the current list. They are handy when a list carries string or non sequential keys and you want just one side of the pairs.

```php
$scores = new ArrayList(['alice' => 30, 'bob' => 17, 'carol' => 42]);

$scores->keys();   // ArrayList of ['alice', 'bob', 'carol']
$scores->values(); // ArrayList of [30, 17, 42]
```

`firstKey()` and `lastKey()` return the raw array key of the first or last entry, or `null` when the list is empty. Unlike `first()` and `last()`, which return the value, these return the key itself.

```php
$scores->firstKey(); // 'alice'
$scores->lastKey();  // 'carol'

(new ArrayList())->firstKey(); // null
```

## Iterating without transforming

`each()` runs a callback for every item and key purely for its side effects and returns the same instance, so it does not build a new list the way `map()` does. Use it when you want to act on each item, for example asserting on it or logging it, while keeping the original list to carry on with.

```php
public function each(callable $fn): static
```

```php
$products = ArrayList::of([
    ['id' => 1, 'name' => 'Keyboard'],
    ['id' => 2, 'name' => 'Mouse'],
]);

$products->each(static function (array $product, int $index): void {
    assert($product['id'] > 0);
});

// $products is the same instance, unchanged.
```

The callback receives the value first and the key second.

## Extracting and reshaping nested data

`column()` pulls a single field out of every item, where each item is an array or an object. It accepts one or more keys as a variadic, and each extra key drills one level deeper into the result of the previous one.

```php
public function column(string|int ...$columns): static
```

```php
$products = ArrayList::of([
    ['id' => 1, 'name' => 'Keyboard'],
    ['id' => 2, 'name' => 'Mouse'],
]);

$products->column('id');   // ArrayList of [1, 2]
$products->column('name'); // ArrayList of ['Keyboard', 'Mouse']
```

`collapse()` flattens one level of nesting into a single flat list. Nested plain arrays and nested `ArrayList` items are merged in order, while scalar items are kept as is.

```php
public function collapse(): static
```

```php
$nested = new ArrayList([
    [1, 2],
    [3, 4],
    new ArrayList([5, 6]),
]);

$nested->collapse(); // ArrayList of [1, 2, 3, 4, 5, 6]
```

## Picking specific keys with only

`only()` maps over the list and reduces every item to the given keys. For an array item it keeps only those keys, for an object that defines its own `only()` method it delegates to that method, and any other item is passed through unchanged.

```php
public function only(array $keys): static
```

```php
$people = ArrayList::of([
    ['id' => 1, 'name' => 'Alice', 'age' => 30],
    ['id' => 2, 'name' => 'Bob', 'age' => 17],
]);

$people->only(['id', 'name']);
// ArrayList of [
//     ['id' => 1, 'name' => 'Alice'],
//     ['id' => 2, 'name' => 'Bob'],
// ]
```

## Cloning a list

`clone()` returns a fresh instance of the same class holding the same data. It is a one line way to branch off a copy before further in place mutation on an `ArrayList`, so the original stays as it was.

```php
public function clone(): static
```

```php
$original = new ArrayList([1, 2, 3]);
$copy = $original->clone();

$copy->append(4); // $copy is [1, 2, 3, 4], $original stays [1, 2, 3]
```
