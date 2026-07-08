---
outline: deep

cards:
    highlights:
        -   title: ArrayList
            code: true
            details: 'Mutable, chainable list with map, filter, reduce, chunk, groupBy and more.'
            link: /collection/api/ArrayList
        -   title: ReadonlyArrayList
            code: true
            details: 'Immutable list that throws when you try to write through array access.'
            link: /collection/api/ReadonlyArrayList
        -   title: StringArrayList
            code: true
            details: 'Typed list that validates every item and adds string helpers.'
            link: /collection/api/StringArrayList
        -   title: Map
            code: true
            details: 'String keyed dictionary with get, set, has, unset and merge.'
            link: /collection/api/Map
        -   title: CacheMap
            code: true
            details: 'Map that memoizes the result of a callable per key.'
            link: /collection/api/CacheMap
        -   title: Paginated
            code: true
            details: 'Immutable value object wrapping a page of results with metadata.'
            link: /collection/api/Paginated
---

# Collection

The Collection package provides the list and map primitives used across the rest of Raxos. It ships an ordered, chainable `ArrayList` with a full set of functional operations, a set of typed list variants that validate their items, string keyed `Map` dictionaries, and a small `Paginated` value object for API responses. Every type is iterable, countable and JSON serializable, so it drops straight into HTTP responses and ORM relations.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Array lists](/collection/array-lists): `ArrayList` and `ReadonlyArrayList`, construction, array access, iteration and the full set of chainable operations.
- [Typed lists](/collection/typed-lists): `StringArrayList`, `IntArrayList` and `NumberArrayList` and how item validation works.
- [Maps](/collection/maps): `Map`, `CacheMap` and `ReadonlyMap` string keyed dictionaries.
- [Pagination](/collection/pagination): the `Paginated` value object and its JSON shape.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Collection\ArrayList;

$numbers = ArrayList::of([1, 2, 3, 4, 5]);

$result = $numbers
    ->filter(static fn(int $number): bool => $number % 2 === 1)
    ->map(static fn(int $number): int => $number * 10);

$result->toArray(); // [10, 30, 50]
```

## Installation

Install the package with Composer and check the requirements on the [installation](/collection/installation) page.

```shell
composer require raxos/collection
```
