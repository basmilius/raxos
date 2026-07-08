---
outline: deep
---

# Connections

A connection wraps a PDO instance and exposes it through one `ConnectionInterface`, regardless of the underlying database. The package ships three drivers, all extending the abstract `Connection` base class:

- `MySql` for MySQL servers.
- `MariaDb` for MariaDB servers.
- `SQLite` for file backed or in memory SQLite databases.

## Creating a connection

Each driver exposes a static factory that builds the PDO DSN for you.

### MySQL and MariaDB

`MySql::createFromOptions()` and `MariaDb::createFromOptions()` take discrete options and assemble the DSN. Provide either a host (with an optional port) or a unix socket, plus a database name.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Connection\{MariaDb, MySql};

$mysql = MySql::createFromOptions(
    host: '127.0.0.1',
    port: 3306,
    database: 'app',
    username: 'root',
    password: 'secret',
);

$mariadb = MariaDb::createFromOptions(
    unixSocket: '/var/run/mysqld/mysqld.sock',
    database: 'app',
    username: 'app',
);
```

The `charset` defaults to `utf8mb4`. Passing both a `unixSocket` and a `host` or `port` throws an `InvalidOptionException`, and leaving out the host, port, socket or database throws a `MissingOptionException`.

### SQLite

`SQLite::createFromFile()` opens a database stored on disk, `SQLite::createFromInMemory()` opens a transient database that lives only for the duration of the process.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Connection\SQLite;

$disk = SQLite::createFromFile(__DIR__ . '/data/app.sqlite');
$memory = SQLite::createFromInMemory();
```

## Registering and resolving connections

The `Db` facade keeps a registry of connections keyed by an id. Register a connection once, then resolve it anywhere.

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

// Register a second connection under an explicit id.
Db::register(MySql::createFromOptions(
    host: 'reports.internal',
    database: 'analytics',
    username: 'reader',
), 'analytics');
```

Without an id, a connection is stored as the default. `Db::get()` returns the connection for an id (or the default when the id is omitted) or `null` when nothing is registered, while `Db::getOrFail()` throws an `InvalidConnectionException` instead of returning `null`.

```php
$default = Db::getOrFail();
$analytics = Db::getOrFail('analytics');
```

Models resolve their connection through the same registry, so registering a default connection is usually all the setup an ORM heavy application needs.

::: tip
Connections connect lazily. The PDO instance is only created the first time `Db::get()` (or the model layer) actually needs it, so registering a connection at boot does not open a socket until the first query runs.
:::

## What a connection exposes

Once resolved, a connection is your entry point for everything below the ORM:

- Start a query with `query()` (see the [query builder](/database/query-builder)).
- Prepare a statement with `prepare()`.
- Manage transactions with `transaction()`, `commit()` and `rollBack()` (see [transactions](/database/transactions-and-logging)).
- Quote a value with `quote()` for use in a raw query.
- Introspect the schema with `tableExists()`, `tableColumns()` and `tableColumnExists()`.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

$connection = Db::getOrFail();

if ($connection->tableExists('users')) {
    $columns = $connection->tableColumns('users');
}
```

The `Db` facade mirrors most of these methods as static shortcuts that delegate to the resolved connection, so `Db::query()`, `Db::transaction()` and `Db::quote()` are equivalent to calling the same method on `Db::getOrFail()`.

See the [Connection API reference](/database/api/Connection) for the full driver surface and the [Db API reference](/database/api/Db) for the facade.
