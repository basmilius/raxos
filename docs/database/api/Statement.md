---
outline: deep
---

# Statement

`Raxos\Database\Query\Statement` wraps a prepared `PDOStatement`. It binds parameters, executes the query and turns rows into arrays, `stdClass` values or hydrated models. A statement is normally created for you by `Query`, but you can build one directly with `Connection::prepare()` or `Db::prepare()`.

```php
class Statement implements StatementInterface
```

## Binding

| Method | Description |
| --- | --- |
| `bind(string $name, bool\|string\|int\|float\|null $value, ?int $type = null): static` | Binds a parameter value, inferring the PDO type from the value when `$type` is omitted. |

## Fetching

| Method | Description |
| --- | --- |
| `run(): int` | Executes the statement and returns the affected row count. |
| `single(int $fetchMode = PDO::FETCH_ASSOC): Model\|stdClass\|array\|null` | Executes and fetches the first row, hydrating a model when one is assigned. |
| `array(int $fetchMode = PDO::FETCH_ASSOC): array` | Executes and fetches every row. |
| `arrayList(int $fetchMode = PDO::FETCH_ASSOC): ArrayListInterface\|ModelArrayList` | Executes and returns the rows as a collection. |
| `cursor(int $fetchMode = PDO::FETCH_ASSOC): Generator` | Executes and yields rows one at a time. |
| `fetchColumn(int $index = 0): mixed` | Executes and returns a single column value from the first row. |
| `rowCount(): int` | Returns the affected row count of the last execution. |

## Model binding

| Method | Description |
| --- | --- |
| `withModel(string $class): static` | Hydrates fetched rows into instances of the given model. |
| `withoutModel(): static` | Removes the model binding, so rows come back raw. |
| `eagerLoad(array $relationships): void` | Sets the relations to eager load for hydrated models. |
| `eagerLoadDisable(array $relationships): void` | Sets the relations to skip when eager loading. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

$statement = Db::prepare('select * from users where id = :id');
$statement->bind('id', 'usr_1');

$user = $statement->single();
```

When a statement is prepared from a model query, `single()`, `array()` and `arrayList()` return hydrated model instances and honor any eager loading configured on the query. See [Query](/database/api/Query) for how those are set up.
