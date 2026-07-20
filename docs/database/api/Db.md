---
outline: deep
---

# Db

`Raxos\Database\Db` is the static facade for the connection registry. It stores connections by id and delegates common operations (queries, execution, transactions, quoting and schema checks) to the resolved connection.

```php
class Db
```

## Resolving connections

| Method | Description |
| --- | --- |
| `static get(?string $id = null): ?ConnectionInterface` | Returns the connection for the id, or the default connection, connecting it on first use. Returns `null` when none is registered. |
| `static getOrFail(?string $id = null): ConnectionInterface` | Same as `get()`, but throws `InvalidConnectionException` when no connection is registered. |
| `static register(ConnectionInterface $connection, ?string $id = null): void` | Registers a connection under the id, or as the default connection. |
| `static unregister(string $id): void` | Removes a registered connection. |

## Delegated operations

Each of these resolves the connection (defaulting to the registered default) and forwards the call.

| Method | Description |
| --- | --- |
| `static query(?string $id = null): QueryInterface` | Starts a new query on the resolved connection. |
| `static prepare(QueryInterface\|string $query, array $options = [], ?string $id = null): StatementInterface` | Prepares a statement. |
| `static execute(QueryInterface\|string $query, ?string $id = null): int` | Executes a statement and returns the affected row count. |
| `static column(QueryInterface\|string $query, ?string $id = null): string\|int` | Executes and returns the first column of the first row. |
| `static quote(BackedEnum\|string\|int\|float\|bool $value, int $type = PDO::PARAM_STR, ?string $id = null): string` | Quotes a value for a raw query. |
| `static lastInsertId(?string $name = null, ?string $id = null): string` | Returns the last insert id as a string. |
| `static lastInsertIdInteger(?string $name = null, ?string $id = null): int` | Returns the last insert id as an int. |

## Transactions

| Method | Description |
| --- | --- |
| `static transaction(?string $id = null): bool` | Begins a transaction, or a savepoint when already inside one. |
| `static commit(?string $id = null): bool` | Commits the current transaction or savepoint. |
| `static rollBack(?string $id = null): bool` | Rolls back the current transaction or savepoint. |
| `static inTransaction(?string $id = null): bool` | Returns `true` when a transaction is active. |

## Schema introspection

| Method | Description |
| --- | --- |
| `static tableExists(string $table, ?string $id = null): bool` | Returns `true` if the table exists. |
| `static tableColumns(string $table, ?string $id = null): array` | Returns the column names of a table. |
| `static tableColumnExists(string $table, string $column, ?string $id = null): bool` | Returns `true` if the column exists in the table. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Connection\MySql;
use Raxos\Database\Db;

Db::register(MySql::createFromOptions(
    host: 'localhost',
    database: 'app',
    username: 'root',
));

Db::transaction();

try {
    Db::query()
        ->update('users', ['is_active' => 1])
        ->where('id', 'usr_1')
        ->run();

    Db::commit();
} catch (Throwable $err) {
    Db::rollBack();

    throw $err;
}
```

See [connections](/database/connections) for how connections are created and registered.
