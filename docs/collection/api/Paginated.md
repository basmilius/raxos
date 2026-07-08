---
outline: deep
---

# Paginated

`Raxos\Collection\Paginated` is an immutable value object describing one page of results together with the total item and page counts. It serializes to a snake_case JSON shape suitable for an API response body.

```php
readonly class Paginated implements JsonSerializable
```

## Construction

### __construct

```php
public function __construct(
    ArrayListInterface $items,
    int $page,
    int $pageSize,
    int $pages,
    int $total
)
```

Creates a paginated result set. All arguments are exposed as public readonly properties: `$items`, `$page`, `$pageSize`, `$pages` and `$total`.

## Methods

### jsonSerialize

```php
public function jsonSerialize(): array
```

Serializes to `items`, `page`, `page_size`, `pages` and `total`. Note that `pageSize` is emitted as `page_size`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\{ArrayList, Paginated};

$page = new Paginated(
    items: new ArrayList(['Alice', 'Bob']),
    page: 2,
    pageSize: 2,
    pages: 5,
    total: 9,
);

json_encode($page);
// {"items":["Alice","Bob"],"page":2,"page_size":2,"pages":5,"total":9}
```

See also the [pagination](/collection/pagination) concept page.
