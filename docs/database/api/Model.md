---
outline: deep
---

# Model

`Raxos\Database\Orm\Model` is the abstract base class for ORM models. It is backed by a `Backbone` that tracks the column data, exposes array and property style access through the [raxos/foundation](/foundation/) access traits, and provides array and JSON export with visibility rules. The static finder methods come from the `Queryable` trait it mixes in.

```php
abstract class Model implements
    AccessInterface,
    ArrayableInterface,
    JsonSerializable,
    QueryableInterface,
    Stringable
```

## Instance methods

| Method | Description |
| --- | --- |
| `save(): void` | Inserts or updates the record for this instance. |
| `destroy(): void` | Deletes the record for this instance. |
| `toArray(): array` | Converts the model to an array, honoring `#[Hidden]`, `#[Visible]` and any per instance overrides. |
| `jsonSerialize(): array` | Reuses `toArray()` for `json_encode()`. |
| `makeHidden(array\|string $keys): static` | Returns a clone with the given keys hidden from export. |
| `makeVisible(array\|string $keys): static` | Returns a clone with the given keys forced visible in export. |
| `only(array\|string $keys): static` | Returns a clone that exports only the given keys. |

## Static query starters (Queryable)

| Method | Description |
| --- | --- |
| `static query(bool $prepared = true): QueryInterface` | Starts a new query bound to the model. |
| `static select(Select\|QueryValueInterface\|Stringable\|array\|string\|int $keys = []): QueryInterface` | Starts a select query bound to the model. |
| `static selectDistinct(...): QueryInterface` | Starts a select distinct query. |
| `static where(...): QueryInterface` | Shortcut for `select()->where(...)`. |
| `static having(...): QueryInterface` | Shortcut for `select()->having(...)`. |
| `static table(): string` | Returns the model's table name. |
| `static col(string $key): ColumnLiteral` | Returns the fully qualified column literal for a key. |

## Static finders (Queryable)

| Method | Description |
| --- | --- |
| `static all(int $offset = 0, int $limit = 20): ArrayListInterface` | Returns a page of all records. |
| `static single(array\|string\|int $primaryKey): ?static` | Finds one record by primary key, using the identity cache when possible. |
| `static singleOrFail(array\|string\|int $primaryKey): static` | Same as `single()`, but throws `NotFoundException` when nothing is found. |
| `static find(array $primaryKeys): ModelArrayList` | Finds several records by primary key, using the cache where possible. |
| `static exists(array\|string\|int $primaryKey): bool` | Returns `true` if a record with the given primary key exists. |
| `static delete(array\|string\|int $primaryKey): void` | Deletes a record by primary key without loading it. |
| `static update(array\|string\|int $primaryKey, array $values): void` | Updates a record by primary key with the given column values. |

## Relation queries

Calling a relation name as a method returns its query, so you can refine it before executing:

```php
$posts = $user->posts()
    ->where('is_published', 1)
    ->arrayList();
```

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, PrimaryKey, Table};

#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $name;
}

$user = User::singleOrFail('usr_1');
$user->name = 'Bas';
$user->save();

$active = User::where('is_active', 1)->arrayList();
```

## Complete Queryable static methods

Beyond the starters and finders above, the `Queryable` trait exposes the full `where` and `having` shortcut families, the extra select variants and the aliased column helper. Each `where`/`having` shortcut starts a `select()` and applies the condition. See [querying models](/database/orm/querying) for the narrative version.

| Method | Description |
| --- | --- |
| `static whereIn(ColumnLiteral $column, ArrayableInterface\|array $options): QueryInterface` | Shortcut for `select()->whereIn(...)`. |
| `static whereNotIn(ColumnLiteral $column, ArrayableInterface\|array $options): QueryInterface` | Shortcut for `select()->whereNotIn(...)`. |
| `static whereNull(ColumnLiteral $column): QueryInterface` | Shortcut for `select()->whereNull(...)`. |
| `static whereNotNull(ColumnLiteral $column): QueryInterface` | Shortcut for `select()->whereNotNull(...)`. |
| `static whereExists(QueryInterface $query): QueryInterface` | Shortcut for `select()->whereExists(...)`. |
| `static whereNotExists(QueryInterface $query): QueryInterface` | Shortcut for `select()->whereNotExists(...)`. |
| `static having(...): QueryInterface` | Shortcut for `select()->having(...)`. |
| `static havingIn(ColumnLiteral $column, ArrayableInterface\|array $options): QueryInterface` | Shortcut for `select()->havingIn(...)`. |
| `static havingNotIn(ColumnLiteral $column, ArrayableInterface\|array $options): QueryInterface` | Shortcut for `select()->havingNotIn(...)`. |
| `static havingNull(ColumnLiteral $column): QueryInterface` | Shortcut for `select()->havingNull(...)`. |
| `static havingNotNull(ColumnLiteral $column): QueryInterface` | Shortcut for `select()->havingNotNull(...)`. |
| `static havingExists(QueryInterface $query): QueryInterface` | Shortcut for `select()->havingExists(...)`. |
| `static havingNotExists(QueryInterface $query): QueryInterface` | Shortcut for `select()->havingNotExists(...)`. |
| `static selectFoundRows(Select\|QueryValueInterface\|Stringable\|array\|string\|int $keys = [], bool $prepared = true): QueryInterface` | Starts a select that adds `SQL_CALC_FOUND_ROWS`. |
| `static selectSuffix(string $suffix, Select\|QueryValueInterface\|Stringable\|array\|string\|int $keys = [], bool $prepared = true): QueryInterface` | Starts a select with a raw suffix after the select keyword. |
| `static alias(string $key, string $table): ColumnLiteral` | Returns the fully qualified column literal for a key against an aliased table. |

## Queryable hooks

Through `QueryableInterface`, a model implements two overridable static hooks. Both are no-ops on `Model` by default; override them to add computed columns and their supporting joins to every query for the model.

| Method | Description |
| --- | --- |
| `static getQueryableColumns(Select $select): Select` | Adds extra columns to every model query. |
| `static getQueryableJoins(QueryInterface $query): QueryInterface` | Adds the joins those columns rely on. |

See [models](/database/orm/models) for a worked example that combines both hooks with a `#[Computed]` property.

See [models](/database/orm/models) for the attribute driven definition, [relations](/database/orm/relations) for links between models, and [casters, embeddables and polymorphic models](/database/orm/casters-and-embeddables) for value conversion.
