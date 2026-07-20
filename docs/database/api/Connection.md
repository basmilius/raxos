---
outline: deep
---

# Connection, MySql, MariaDb and SQLite

`Raxos\Database\Connection\Connection` is the abstract base class shared by every driver. It wraps a lazily created PDO instance and implements transactions, quoting, statement preparation and schema introspection. The concrete drivers add the DSN and dialect specific pieces.

```php
abstract class Connection implements ConnectionInterface
```

## Base methods

| Method | Description |
| --- | --- |
| `abstract connect(): void` | Opens the underlying PDO connection, implemented per driver. |
| `disconnect(): void` | Closes the connection by dropping the PDO instance. |
| `ping(): bool` | Checks whether the connection is still alive. |
| `query(): QueryInterface` | Starts a new query builder for this connection's dialect. |
| `prepare(string\|QueryInterface $query, array $options = []): StatementInterface` | Prepares a statement for a query or raw SQL string. |
| `execute(string\|QueryInterface $query): int` | Executes a statement and returns the affected row count. |
| `transaction(): bool` | Begins a transaction, or a savepoint when one is already active. |
| `commit(): bool` | Commits the current transaction or releases the current savepoint. |
| `rollBack(): bool` | Rolls back the current transaction or the current savepoint. |
| `quote(BackedEnum\|float\|bool\|int\|string $value, int $type = PDO::PARAM_STR): string` | Quotes a value for use in a raw query. |
| `tableExists(string $table): bool` | Returns `true` if the table exists, caching the schema after the first lookup. |
| `tableColumns(string $table): array` | Returns the column names of a table. |
| `tableColumnExists(string $table, string $column): bool` | Returns `true` if the column exists in the table. |

Read only state includes `connected` (whether the PDO instance exists), `inTransaction` and the `logger`, `cache` and `grammar` collaborators passed at construction.

## MySql

```php
class MySql extends Connection
```

Uses the MySQL grammar and PDO MySQL driver.

```php
static createFromOptions(
    ?string $host = null,
    ?int $port = null,
    ?string $database = null,
    ?string $unixSocket = null,
    string $charset = 'utf8mb4',
    ?string $username = null,
    ?string $password = null,
    ?array $options = null,
    ?CacheInterface $cache = new Cache(),
    ?Logger $logger = new Logger()
): self
```

Builds a MySQL DSN from discrete options (either host and port, or a unix socket) plus a database name, and returns a ready connection.

## MariaDb

```php
class MariaDb extends Connection
```

The same shape as `MySql`, using the MariaDB grammar. Its `createFromOptions()` takes the identical parameters.

## SQLite

```php
class SQLite extends Connection
```

For file backed or in memory databases.

| Method | Description |
| --- | --- |
| `static createFromFile(string $path, ?array $options = null, ?CacheInterface $cache = new Cache(), ?Logger $logger = new Logger()): self` | Opens a SQLite database stored at the given file path. |
| `static createFromInMemory(?array $options = null, ?CacheInterface $cache = new Cache(), ?Logger $logger = new Logger()): self` | Opens a transient in memory SQLite database. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Connection\{MariaDb, SQLite};

$mariadb = MariaDb::createFromOptions(
    host: '127.0.0.1',
    port: 3306,
    database: 'app',
    username: 'app',
    password: 'secret',
);

$sqlite = SQLite::createFromFile('/var/data/app.sqlite');
```

See [connections](/database/connections) for the registration flow through [Db](/database/api/Db).
