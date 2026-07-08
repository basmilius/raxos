---
outline: deep
---

# ArrayUtil

`Raxos\Foundation\Util\ArrayUtil` is a `final` class of static helper methods for working with plain PHP arrays and iterables.

See the [Util classes concept page](/foundation/utilities) for an overview of all utility classes.

## Signature

```php
namespace Raxos\Foundation\Util;

final class ArrayUtil
```

## Methods

```php
public static function ensureArray(ArrayListInterface|ArrayableInterface|iterable $items): array
```
Normalises an iterable, `ArrayableInterface` or `ArrayListInterface` into a plain array.

```php
public static function flatten(array $arr, int $depth = 25): array
```
Flattens a nested array up to the given depth.

```php
public static function groupBy(array $arr, float|int|string $key): array
```
Groups a list of arrays by the value at the given key.

```php
public static function in(array $arr, array $items, bool $all = false): bool
```
Checks whether any (or, with `$all` true, all) of the given items are present in the array.

```php
public static function only(array $arr, array $keys): array
```
Returns a subset of the array containing only the given keys.

```php
public static function first(array $items, ?callable $predicate = null, mixed $defaultValue = null): mixed
```
Returns the first element, optionally the first matching a predicate, or the default value.

```php
public static function last(array $items, ?callable $predicate = null, mixed $defaultValue = null): mixed
```
Returns the last element, optionally the last matching a predicate, or the default value.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\ArrayUtil;

$rows = [
    ['type' => 'a', 'value' => 1],
    ['type' => 'b', 'value' => 2],
    ['type' => 'a', 'value' => 3]
];

$byType = ArrayUtil::groupBy($rows, 'type');

$firstB = ArrayUtil::first(
    $rows,
    static fn(array $row): bool => $row['type'] === 'b'
);
```
