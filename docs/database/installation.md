---
outline: deep
---

# Installation

Install Database with Composer.

```shell
composer require raxos/database
```

## Requirements

- PHP 8.5 or newer.
- The following PHP extensions:
    - `ext-pdo`
    - `pdo_mysql` for MySql and MariaDb connections.
    - `pdo_sqlite` for SQLite connections.

Only the PDO driver for the database you actually connect to is needed. A project that talks to MySQL or MariaDB requires `pdo_mysql`, a project on SQLite requires `pdo_sqlite`, and a project that uses both requires both.

## Raxos dependencies

Database depends on one other Raxos package:

- [raxos/foundation](/foundation/): supplies the `ArrayAccessible` and `ObjectAccessible` access traits used by `Model`, the `Stopwatch` timing helper used by the query logger, and shared utilities.

The `Query` and `Statement` classes also return collection types (`ArrayList`, `ModelArrayList` and `Paginated`) that come from the collection package pulled in transitively.

Return to the [Database introduction](/database/).
