---
outline: deep
---

# Pagination

`Paginated` is a small immutable value object that describes a single page of results together with the counts a client needs to render pagination controls. It wraps an `ArrayListInterface` and implements `JsonSerializable`, so you can return it directly as an API response body.

## Creating a paginated result

```php
<?php
declare(strict_types=1);

use Raxos\Collection\{ArrayList, Paginated};

$items = new ArrayList(['Alice', 'Bob', 'Carol']);

$page = new Paginated(
    items: $items,
    page: 1,
    pageSize: 25,
    pages: 4,
    total: 87,
);
```

All five constructor arguments are exposed as public readonly properties: `$items`, `$page`, `$pageSize`, `$pages` and `$total`.

## JSON shape

`jsonSerialize()` produces a snake_case shape that is ready to serve as a response body.

```php
echo json_encode($page);
```

```json
{
    "items": ["Alice", "Bob", "Carol"],
    "page": 1,
    "page_size": 25,
    "pages": 4,
    "total": 87
}
```

Note that `pageSize` becomes `page_size` in the output. Because `items` is itself JSON serializable, the wrapped list is encoded inline.
