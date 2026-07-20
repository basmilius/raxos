---
outline: deep
---

# Query builder

The query builder assembles a SQL statement from fluent method calls and executes it through a prepared `Statement`. Every driver returns a dialect specific subclass of the abstract `Query`, but the public surface is identical across MySQL, MariaDB and SQLite.

## Starting a query

Start a raw query from a connection, or a model bound query from a model.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

// Raw query, rows come back as arrays.
$query = Db::query();

// Model query, rows come back hydrated as models.
$query = User::select();
```

`Db::query()` (or `Connection::query()`) returns a builder that fetches associative arrays. `Model::query()` and `Model::select()` return a builder bound to that model, so terminal methods hydrate the rows into model instances. See [models](/database/orm/models) for the model side.

## Selecting

`select()` accepts nothing (which becomes `*`), a single column, a list of columns, or a map of alias to column.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

$rows = Db::query()
    ->select(['id', 'name', 'email'])
    ->from('users')
    ->where('is_active', 1)
    ->orderBy('name')
    ->limit(25)
    ->array();
```

Related helpers: `selectDistinct()` and `selectSuffix()`. The `from()` method takes a table name, a list of tables, or another query as a sub query, with an optional alias.

`selectFoundRows()` is a shortcut for `selectSuffix('sql_calc_found_rows', ...)`: it adds `SQL_CALC_FOUND_ROWS` to a select so a follow up `found_rows()` call reports the total row count ignoring the limit.

## Where clauses

`where()` chains conditions with `and`, `orWhere()` with `or`. Passing two arguments implies an `=` comparison; passing three uses the given operator.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

$rows = Db::query()
    ->select()
    ->from('orders')
    ->where('status', 'paid')
    ->where('total', '>', 100)
    ->orWhere('status', 'refunded')
    ->array();
```

There is a full family of null and set helpers: `whereNull()`, `whereNotNull()`, `whereIn()`, `whereNotIn()`, `whereExists()`, `whereNotExists()` and their `orWhere...` counterparts. The same set exists for `having()`.

## Joins

The join family covers every SQL join. Each takes a table name and an optional callback that receives the query so you can add `on()` conditions.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Database\Query\QueryInterface;
use Raxos\Database\Db;

$rows = Db::query()
    ->select(['users.id', 'profiles.bio'])
    ->from('users')
    ->leftJoin('profiles', static fn(QueryInterface $query) => $query
        ->on('profiles.user_id', 'users.id'))
    ->array();
```

Available joins: `join()`, `innerJoin()`, `leftJoin()`, `leftOuterJoin()`, `rightJoin()` and `fullJoin()`.

## Grouping and ordering

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

$rows = Db::query()
    ->select(['country', 'count(*)'])
    ->from('users')
    ->groupBy('country')
    ->having('count(*)', '>', 10)
    ->orderByDesc('country')
    ->array();
```

`orderBy()` accepts a column, a list of columns, or a column with a trailing `asc` or `desc`; `orderByAsc()` and `orderByDesc()` are explicit shortcuts. `limit()` takes an optional second offset argument, and `offset()` sets it on its own.

## Inserting

`insertIntoValues()` builds an insert from either a single column to value map or a list of rows. `insertInto()` plus `values()` gives you the same in two steps.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

// Single row.
Db::query()
    ->insertIntoValues('users', [
        'id' => 'usr_1',
        'name' => 'Bas',
    ])
    ->run();

// Multiple rows.
Db::query()
    ->insertIntoValues('tags', [
        ['name' => 'php'],
        ['name' => 'sql'],
    ])
    ->run();
```

`insertIgnoreIntoValues()`, `replaceIntoValues()` and `onDuplicateKeyUpdate()` handle conflict cases:

```php
Db::query()
    ->insertIntoValues('counters', ['key' => 'hits', 'value' => 1])
    ->onDuplicateKeyUpdate('value')
    ->run();
```

## Updating and deleting

`update()` optionally takes a map of column to value; `set()` adds assignments one at a time. `deleteFrom()` builds a delete.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

Db::query()
    ->update('users', ['name' => 'Bas Milius'])
    ->where('id', 'usr_1')
    ->run();

Db::query()
    ->deleteFrom('sessions')
    ->where('expires_on', '<', 1710000000)
    ->run();
```

## Raw fragments

`literal()` inserts a raw SQL fragment unquoted, `stringLiteral()` inserts a quoted string literal. Both live in the `Raxos\Database\Query` namespace.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;
use function Raxos\Database\Query\{literal, stringLiteral};

Db::query()
    ->update('articles', ['views' => literal('views + 1')])
    ->where('slug', stringLiteral('hello-world'))
    ->run();
```

For structured expressions (comparisons, aggregates, dates, string functions) use the [Expr helper](/database/api/Expr).

## Partials

A partial is a reusable sub query fragment that behaves as an expression. Wrap a builder closure in `partial()`; the closure receives the connection of the host query and is only invoked when the partial is compiled, so a partial can be constructed without a live connection and reused anywhere an expression is accepted — most notably inside `Expr::exists()`.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Database\ConnectionInterface;
use Raxos\Contract\Database\Query\QueryInterface;
use Raxos\Database\Db;
use Raxos\Database\Query\Expr;
use function Raxos\Database\Query\{column, partial};

// A correlated "has an order line" fragment, reusable across queries.
$hasOrderLine = partial(static fn(ConnectionInterface $connection): QueryInterface => $connection->query()
    ->select(1)
    ->from('order_line')
    ->where('product_id', column('id', 'product')));

Db::query()
    ->select('*')
    ->from('product')
    ->where(Expr::exists($hasOrderLine))
    ->array();
```

Because a partial builds its sub query lazily with the host connection, the same partial works across connections and never depends on the global `Db::query()`. See the [Partial reference](/database/api/Partial).

## Executing a query

Terminal methods run the statement and shape the result:

| Method | Returns |
| --- | --- |
| `array()` | All rows as a plain array. |
| `arrayList()` | An `ArrayList`, or a `ModelArrayList` for model queries. |
| `single()` | The first row, or `null`. |
| `singleOrFail()` | The first row, or throws `MissingResultException`. |
| `cursor()` | A `Generator` that yields rows one at a time. |
| `run()` | The affected row count for a write. |
| `paginate()` | A `Paginated` page including a total count. |

```php
<?php
declare(strict_types=1);

$page = User::select()
    ->where('is_active', 1)
    ->paginate(offset: 0, limit: 20);
```

`toSql()` compiles the query to a SQL string without executing it, which is handy for debugging or for building sub queries.

See the [Query API reference](/database/api/Query) and the [Statement API reference](/database/api/Statement) for the full method list.
