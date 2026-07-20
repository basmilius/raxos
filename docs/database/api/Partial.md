---
outline: deep
---

# Partial

`Raxos\Database\Query\Partial` is a reusable sub query fragment that behaves as a query expression. It wraps a closure that builds a query; the closure receives the connection of the host query and is only invoked when the partial is compiled. A partial can therefore be constructed without a live connection and used anywhere a `QueryExpressionInterface` is accepted — inside `Expr::exists()`, a `select`, a `where`, and so on.

```php
final readonly class Partial implements QueryExpressionInterface
```

## Creating a partial

Use the `partial()` helper in the `Raxos\Database\Query` namespace instead of constructing the class directly:

```php
use function Raxos\Database\Query\partial;
```

| Function | Description |
| --- | --- |
| `partial(Closure $query): Partial` | Wraps a `Closure(ConnectionInterface): QueryInterface` as a reusable, lazily built sub query. |

## Behaviour

When the partial is compiled it invokes the closure with the host connection, then merges the resulting query as a parenthesized sub query — the same way `Expr::subQuery()` does, except the query is built lazily. Because building is deferred to compile time, the partial runs on the same connection as the host query and never depends on the global `Db::query()`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Database\ConnectionInterface;
use Raxos\Contract\Database\Query\QueryInterface;
use Raxos\Database\Db;
use Raxos\Database\Query\Expr;
use function Raxos\Database\Query\{column, partial};

// A reusable "has a paid order line" fragment.
$hasPaidOrderLine = partial(static fn(ConnectionInterface $connection): QueryInterface => $connection->query()
    ->select(1)
    ->from('order_line')
    ->where('product_id', column('id', 'product'))
    ->where('status', 'paid'));

$products = Db::query()
    ->select('*')
    ->from('product')
    ->where(Expr::exists($hasPaidOrderLine))
    ->array();
```

See [Expr](/database/api/Expr) for the expression helpers a partial composes with.
