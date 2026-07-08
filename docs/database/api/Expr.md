---
outline: deep
---

# Expr

`Raxos\Database\Query\Expr` builds SQL expressions used inside `where`, `having` and `select` clauses: comparisons, aggregates, date and time functions, numeric and string functions, and control flow. A ready made instance is exposed as the global `expr` constant in the `Raxos\Database\Query` namespace, so you rarely construct it yourself.

```php
final class Expr implements QueryExpressionsInterface
```

Import the constant and use it directly:

```php
use const Raxos\Database\Query\expr;
```

## Comparisons

| Method | Builds |
| --- | --- |
| `eq($left, $right)` | `$left = $right` |
| `gt` / `gte` / `lt` / `lte` | `>`, `>=`, `<`, `<=` |
| `not(QueryExpressionInterface $expr)` | Negates an expression. |
| `in(...$values)` | `in (...)` |
| `between($lower, $upper)` | `between $lower and $upper` |
| `isNull()` / `isNotNull()` | `is null` / `is not null` |

## Aggregates

| Method | Builds |
| --- | --- |
| `count(QueryInterface\|QueryValueInterface\|string\|null $expr = null, bool $distinct = false)` | `count(...)`, or `count(*)` when no expression is given. |
| `avg($expr, bool $distinct = false)` | `avg(...)` |
| `sum($expr, bool $distinct = false)` | `sum(...)` |
| `min($expr, bool $distinct = false)` | `min(...)` |
| `max($expr, bool $distinct = false)` | `max(...)` |
| `groupConcat(...)` | `group_concat(...)` |

## Dates, numbers and strings

The expression builder also covers date and time functions (`now()`, `currentDate()`, `date()`, `dateAdd()`, `dateSub()`, `year()`, `month()`, `day()`, `extract()` and more), numeric functions (`abs()`, `ceil()`, `floor()`, `round()`, `pow()`, arithmetic through `add()`, `mul()`, `div()`, `mod()`) and string functions (`concat()`, `concatWs()`, `matchAgainst()`, `sha1()`).

## Control flow and sub queries

| Method | Builds |
| --- | --- |
| `case(): Expression\CaseStatement` | Starts a case / when / else builder. |
| `when($when, $then)` | A single `when ... then ...` branch. |
| `if($expr, $then, $else)` | `if(...)` |
| `ifNull($a, $b)` / `nullIf($a, $b)` | `ifnull(...)` / `nullif(...)` |
| `exists(QueryInterface\|QueryExpressionInterface $expr)` | `exists (...)`, wrapping a query in a sub query when needed. |
| `subQuery(QueryInterface $query)` | Wraps a query as a parenthesized sub query. |
| `func(string $name, array $params)` | An arbitrary SQL function call. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;
use const Raxos\Database\Query\expr;

// select country, count(*) as total from users group by country having count(*) > 10
$rows = Db::query()
    ->select([
        'country',
        'total' => expr->count(),
    ])
    ->from('users')
    ->groupBy('country')
    ->having(expr->gt(expr->count(), 10))
    ->array();
```

Combine it with a sub query in an `exists` condition:

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;
use const Raxos\Database\Query\expr;

$hasOrders = Db::query()
    ->select()
    ->from('orders')
    ->where('orders.user_id', User::col('id'));

$buyers = User::select()
    ->where(expr->exists($hasOrders))
    ->arrayList();
```

See the [query builder](/database/query-builder) for where these expressions fit into a full statement.
