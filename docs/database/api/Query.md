---
outline: deep
---

# Query

`Raxos\Database\Query\Query` is the fluent SQL query builder returned by `Connection::query()`, `Db::query()` and `Model::query()`. It collects the statement piece by piece and executes it through a prepared `Statement`. Each driver returns a dialect specific subclass, but the public surface below is identical.

```php
abstract class Query implements QueryInterface
```

## Selecting and sources

| Method | Description |
| --- | --- |
| `select(QueryValueInterface\|Stringable\|array\|string\|int $fields = []): static` | Adds or extends the select clause. Empty means `*`. |
| `selectDistinct(QueryValueInterface\|Stringable\|array\|string\|int $fields = []): static` | A `select distinct` clause. |
| `selectSuffix(string $suffix, ...$fields): static` | A select with a raw suffix after the keyword. |
| `from(QueryInterface\|array\|string $tables, ?string $alias = null): static` | Sets the from clause, accepting table names or a sub query. |

## Conditions

| Method | Description |
| --- | --- |
| `where(...$lhs, ...$cmp, ...$rhs): static` | Adds a where condition, chaining with `and`. Two arguments imply `=`. |
| `orWhere(...): static` | Adds a where condition chained with `or`. |
| `whereNull()` / `whereNotNull()` | Adds a null check. |
| `whereIn()` / `whereNotIn()` | Adds an `in` / `not in` check. |
| `whereExists(QueryInterface)` / `whereNotExists()` | Adds an `exists` / `not exists` sub query. |
| `whereHas(string $relation, ?callable $fn = null): static` | Adds a `where exists` based on a model relation. |
| `whereRelation(string $relation, ...): static` | A `whereHas()` that applies a comparison inside the relation. |
| `having(...)`, `havingIn()`, `havingNull()`, ... | The same family for the `having` clause. |

## Joins, grouping and ordering

| Method | Description |
| --- | --- |
| `join()`, `innerJoin()`, `leftJoin()`, `leftOuterJoin()`, `rightJoin()`, `fullJoin()` | The join family; each takes a table and an optional callback for `on()` conditions. |
| `on(...): static` | Adds a join condition, chaining with `and`. |
| `groupBy(QueryLiteralInterface\|array\|string $fields, bool $withRollup = false): static` | Adds a group by clause. |
| `orderBy(QueryLiteralInterface\|array\|string $fields): static` | Adds an order by clause. |
| `orderByAsc()` / `orderByDesc()` | Explicit ascending / descending order. |
| `limit(int $limit, int $offset = 0): static` | Sets the row limit and optional offset. |
| `offset(int $offset): static` | Sets the offset on its own. |

## Writes

| Method | Description |
| --- | --- |
| `insertInto(string $table, array $fields): static` | Starts an insert with the given columns. |
| `insertIntoValues(string $table, array $pairs): static` | Builds an insert from a column to value map or a list of rows. |
| `insertIgnoreIntoValues()` / `replaceIntoValues()` | Insert ignore and replace variants. |
| `values(array $values): static` | Adds a row of values. |
| `onDuplicateKeyUpdate(array\|string $fields): static` | Adds an on duplicate key update clause. |
| `update(string $table, ?array $pairs = null): static` | Starts an update, optionally setting column to value pairs. |
| `set(field, value): static` | Adds a single assignment to an update. |
| `deleteFrom(string $table): static` | Builds a delete for the table. |

## Executing

| Method | Description |
| --- | --- |
| `array(int $fetchMode = PDO::FETCH_ASSOC): array` | Returns all rows as an array. |
| `arrayList(int $fetchMode = PDO::FETCH_ASSOC): ArrayListInterface\|ModelArrayList` | Returns the rows as an `ArrayList`, or a `ModelArrayList` for model queries. |
| `single(int $fetchMode = PDO::FETCH_ASSOC): Model\|stdClass\|array\|null` | Returns the first row or `null`. |
| `singleOrFail(...): Model\|stdClass\|array` | Same as `single()`, but throws `MissingResultException` when nothing is found. |
| `cursor(int $fetchMode = PDO::FETCH_ASSOC): Generator` | Yields rows one at a time. |
| `run(array $options = []): int` | Executes a write and returns the affected row count. |
| `paginate(int $offset, int $limit, ?callable $itemBuilder = null, ?callable $totalBuilder = null): Paginated` | Executes as a page of results, including a total count. |
| `toSql(): string` | Compiles the query to a SQL string. |

## Model helpers

| Method | Description |
| --- | --- |
| `withModel(string $class): static` | Binds the query to a model, so rows hydrate into instances. |
| `withoutModel(): static` | Removes the model binding. |
| `eagerLoad(string\|array $relations): static` | Eager loads the given relations. |
| `eagerLoadDisable(string\|array $relations): static` | Disables eager loading of the given relations. |
| `prime(PrimerInterface\|callable $primer, PrimerTiming $timing = PrimerTiming::AfterRelations): static` | Registers a [primer](/database/api/Primer) that seeds a value across the hydrated batch, before or after relations load. |
| `withDeleted(): static` | Includes soft deleted rows for a model with `#[SoftDelete]`. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;
use function Raxos\Database\Query\literal;

$rows = Db::query()
    ->select(['id', 'name'])
    ->from('users')
    ->where('is_active', 1)
    ->where('created_on', '>', literal('now() - interval 30 day'))
    ->orderByDesc('created_on')
    ->limit(50)
    ->array();
```

For expressions inside `where`, `having` and `select`, see [Expr](/database/api/Expr). For how rows are fetched and hydrated, see [Statement](/database/api/Statement).
