---
outline: deep
---

# Typed lists

The typed list variants extend `ArrayList` and constrain the type of value they hold. They implement `ValidatedArrayListInterface`, which adds a static `validateItem()` method. Whenever you build a typed list through `of()`, every item runs through `validateItem()` first, and an item of the wrong type raises a `CollectionInvalidTypeException`.

There are three built in variants:

- `StringArrayList`: accepts only strings.
- `IntArrayList`: accepts only integers.
- `NumberArrayList`: accepts integers or floats.

## Validation through of()

`of()` is the validating entry point. It walks the items and calls `validateItem()` for each one before constructing the list.

```php
<?php
declare(strict_types=1);

use Raxos\Collection\StringArrayList;
use Raxos\Collection\Error\CollectionInvalidTypeException;

$valid = StringArrayList::of(['one', 'two', 'three']);

try {
    StringArrayList::of(['one', 2, 'three']); // 2 is not a string
} catch (CollectionInvalidTypeException $exception) {
    // Raxos\Collection\StringArrayList only accepts items of type string.
}
```

::: warning
Only `of()` validates. Constructing a typed list directly with `new StringArrayList([...])` or writing through array access does not run `validateItem()`. Prefer `of()` when the input is not already known to be well typed.
:::

## StringArrayList

`StringArrayList` adds two helpers for gluing strings together.

```php
use Raxos\Collection\StringArrayList;

$fruits = StringArrayList::of(['apples', 'pears', 'plums']);

$fruits->join();            // 'apples, pears, plums'
$fruits->join(' | ');       // 'apples | pears | plums'
$fruits->commaCommaAnd();   // 'apples, pears & plums'
```

## IntArrayList

`IntArrayList` adds a `sum()` helper that returns an `int`.

```php
use Raxos\Collection\IntArrayList;

$counts = IntArrayList::of([2, 4, 6]);

$counts->sum(); // 12
```

## NumberArrayList

`NumberArrayList` accepts both integers and floats and returns a `float|int` from `sum()`.

```php
use Raxos\Collection\NumberArrayList;

$amounts = NumberArrayList::of([1.5, 2, 3.25]);

$amounts->sum(); // 6.75
```

Because the typed lists extend `ArrayList`, they inherit every operation from the [array lists](/collection/array-lists) page, such as `map`, `filter` and `sort`. Note that those operations return an instance of the same typed class without re-running validation, so a `map` that changes the value type will not be caught until the next `of()`.
