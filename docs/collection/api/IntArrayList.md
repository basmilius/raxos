---
outline: deep
---

# IntArrayList

`Raxos\Collection\IntArrayList` is an [`ArrayList`](/collection/api/ArrayList) that only accepts integers. It implements `ValidatedArrayListInterface` and adds a `sum` helper.

```php
class IntArrayList extends ArrayList implements ValidatedArrayListInterface
```

## Methods

### sum

```php
public function sum(): int
```

Returns the sum of all items as an `int`.

### validateItem

```php
public static function validateItem(mixed $item): void
```

Throws a `CollectionInvalidTypeException` when the item is not an `int`. This runs for every item when the list is built through `of()`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\IntArrayList;

$quantities = IntArrayList::of([3, 5, 8]);

$quantities->sum(); // 16
```

See also the [typed lists](/collection/typed-lists) concept page.
