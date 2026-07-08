---
outline: deep
---

# StringArrayList

`Raxos\Collection\StringArrayList` is an [`ArrayList`](/collection/api/ArrayList) that only accepts strings. It implements `ValidatedArrayListInterface` and adds two helpers for joining values.

```php
class StringArrayList extends ArrayList implements ValidatedArrayListInterface
```

## Methods

### join

```php
public function join(string $glue = ', '): string
```

Glues all strings together with the given separator.

### commaCommaAnd

```php
public function commaCommaAnd(): string
```

Glues the strings with commas and replaces the last separator with an ampersand, for example `apples, pears & plums`.

### validateItem

```php
public static function validateItem(mixed $item): void
```

Throws a `CollectionInvalidTypeException` when the item is not a string. This runs for every item when the list is built through `of()`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\StringArrayList;

$authors = StringArrayList::of(['Alice', 'Bob', 'Carol']);

$authors->join(' / ');    // 'Alice / Bob / Carol'
$authors->commaCommaAnd(); // 'Alice, Bob & Carol'
```

See also the [typed lists](/collection/typed-lists) concept page.
