---
outline: deep
---

# NumberArrayList

`Raxos\Collection\NumberArrayList` is an [`ArrayList`](/collection/api/ArrayList) that accepts integers or floats. It implements `ValidatedArrayListInterface` and adds a `sum` helper.

```php
class NumberArrayList extends ArrayList implements ValidatedArrayListInterface
```

## Methods

### sum

```php
public function sum(): float|int
```

Returns the sum of all items as a `float` or `int`.

### validateItem

```php
public static function validateItem(mixed $item): void
```

Throws a `CollectionInvalidTypeException` when the item is neither an `int` nor a `float`. This runs for every item when the list is built through `of()`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\NumberArrayList;

$prices = NumberArrayList::of([9.99, 4, 2.5]);

$prices->sum(); // 16.49
```

See also the [typed lists](/collection/typed-lists) concept page.
