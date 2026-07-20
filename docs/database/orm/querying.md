---
outline: deep
---

# Querying models

Every model mixes in the `Raxos\Database\Orm\Queryable` trait, which adds a full static query surface on top of the class. This page gathers that surface into one coherent set: the query starters, the primary key finders, the `where` and `having` shortcut families, the select variants, and the `col()`, `alias()` and `table()` helpers.

## Query starters

Three static methods open a builder bound to the model, so terminal methods hydrate the rows into model instances.

```php
<?php
declare(strict_types=1);

// A bare query, ready for any statement (select, insert, update, delete).
$query = User::query();

// A select query for the model, defaulting to every column.
$query = User::select();

// A select distinct query.
$query = User::selectDistinct(['country']);
```

`query()` starts an empty builder. `select()` and `selectDistinct()` both accept a `Select`, a single column, a list of columns or a map of alias to column, and immediately set the `from` clause to the model's table. From there you chain the [query builder](/database/query-builder) and finish with a terminal such as `arrayList()`, `single()` or `paginate()`.

## Finders

The finder methods resolve records by primary key and lean on the connection's identity cache so a record already in memory is not queried again.

```php
<?php
declare(strict_types=1);

// One record by primary key.
$user = User::single('usr_1');           // ?User
$user = User::singleOrFail('usr_1');     // User, throws NotFoundException when missing

// Several records by primary key, in one batch.
$users = User::find(['usr_1', 'usr_2']); // ModelArrayList

// A page of every record.
$all = User::all(offset: 0, limit: 20);  // ArrayListInterface
```

- `single()` returns the record or `null`; `singleOrFail()` throws `NotFoundException` instead of returning `null`.
- `find(array $primaryKeys)` resolves several records at once. It fills in from the identity cache first and only queries the keys that are still missing, then returns the results as a [ModelArrayList](/database/api/ModelArrayList) in the order the keys were given.
- `all(int $offset = 0, int $limit = 20)` returns a bounded page of every record as an `ArrayListInterface`.

## Primary key actions

`exists()`, `delete()` and `update()` act directly on a primary key without loading the row into a model instance first, and keep the identity cache in sync.

```php
<?php
declare(strict_types=1);

// Existence check; consults the identity cache before querying.
if (User::exists('usr_1')) {
    // ...
}

// Delete by primary key; also drops the record from the identity cache.
User::delete('usr_3');

// Update by primary key with a column to value map.
User::update('usr_1', ['name' => 'Bas Milius']);
```

## Where shortcuts

Each `where` shortcut starts a `select()` and immediately applies that one condition, returning the query so you can keep chaining.

```php
<?php
declare(strict_types=1);

$active = User::where('is_active', 1)
    ->orderBy('name')
    ->arrayList();

$admins = User::whereIn(User::col('role'), ['admin', 'owner'])
    ->arrayList();

$unverified = User::whereNull(User::col('verified_on'))
    ->arrayList();
```

The full family: `where()`, `whereIn()`, `whereNotIn()`, `whereNull()`, `whereNotNull()`, `whereExists()` and `whereNotExists()`. The `where()` shortcut takes the `lhs`, `cmp`, `rhs` triple (two arguments imply an `=` comparison); `whereIn()` and `whereNotIn()` take a `ColumnLiteral` and a list of options; the null variants take a `ColumnLiteral`; the exists variants take a sub query.

## Having shortcuts

The `having` family mirrors the `where` family for the `having` clause, also starting from `select()`.

```php
<?php
declare(strict_types=1);

$popular = Country::selectFoundRows(['code', 'count(*)'])
    ->groupBy(Country::col('code'))
    ->having('count(*)', '>', 10)
    ->arrayList();
```

The full family: `having()`, `havingIn()`, `havingNotIn()`, `havingNull()`, `havingNotNull()`, `havingExists()` and `havingNotExists()`, with the same argument shapes as their `where` counterparts.

## Found rows and raw suffixes

```php
<?php
declare(strict_types=1);

// Adds SQL_CALC_FOUND_ROWS after the select keyword.
$query = User::selectFoundRows(['id', 'name']);

// Injects any other raw suffix after the select keyword.
$query = User::selectSuffix('high_priority', ['id']);
```

`selectFoundRows()` adds `SQL_CALC_FOUND_ROWS` as a select suffix, which pairs with a `found_rows()` follow up to obtain the total row count ignoring the limit. `selectSuffix(string $suffix, ...)` lets you inject any other raw suffix after the `select` keyword.

## Column and table helpers

`col()`, `alias()` and `table()` produce the fully qualified references used in joins, expressions and cross-model comparisons.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Database\Query\QueryInterface;

$lines = OrderLine::select()
    ->join(Product::table(), static fn(QueryInterface $query) => $query
        ->on(Product::col('id'), OrderLine::col('product_id')))
    ->where(OrderLine::col('quantity'), '>', 0)
    ->arrayList();
```

- `col(string $key)` returns a fully qualified `ColumnLiteral` for the model's table. Passing `'*'` yields the wildcard for that table. Results are cached per key.
- `alias(string $key, string $table)` returns the same, but qualified against an aliased table name instead of the model's own table, which is what you need when the model appears more than once in a query.
- `table()` returns the model's mapped table name.

For the raw query builder equivalents of these methods, see the [Query API reference](/database/api/Query). For the collection type the terminals return, see [ModelArrayList](/database/api/ModelArrayList).
