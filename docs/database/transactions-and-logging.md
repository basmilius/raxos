---
outline: deep
---

# Transactions, caching and logging

Beyond building and running queries, a connection manages transactions, keeps a per class identity cache, and can record a query log for debugging.

## Nested transactions with savepoints

`transaction()`, `commit()` and `rollBack()` bracket a unit of work. They are safe to nest: the outermost call opens a real database transaction, and each nested call creates a savepoint instead. Committing a nested level releases its savepoint, rolling one back rewinds to it.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

Db::transaction();

try {
    $user->save();

    Db::transaction(); // Nested: creates a savepoint.

    try {
        $profile->save();
        Db::commit(); // Releases the savepoint.
    } catch (Throwable $err) {
        Db::rollBack(); // Rewinds to the savepoint.

        throw $err;
    }

    Db::commit(); // Commits the outer transaction.
} catch (Throwable $err) {
    Db::rollBack();

    throw $err;
}
```

You can call the same methods directly on a connection (`Db::getOrFail()->transaction()`), which is what the static `Db` shortcuts delegate to.

::: warning
When a nested transaction is rolled back, the outer transaction is marked rollback only. Committing the outer level then throws a `RollbackOnlyTransactionException` and rolls the whole transaction back, because work done between the savepoint and its rollback cannot be safely kept. Calling `commit()` or `rollBack()` outside of any transaction throws a `NotInTransactionException`.
:::

## The model identity cache

Every connection owns an ORM cache that keeps a single instance per model and primary key for the lifetime of the connection. When you load a record whose primary key is already cached, the cached instance is returned instead of a fresh one, so two lookups of the same row give you the same object.

```php
<?php
declare(strict_types=1);

$a = User::single('usr_1');
$b = User::single('usr_1');

// $a and $b are the same instance; the second lookup hit the cache.
```

`Model::single()`, `Model::find()` and `Model::exists()` consult this cache before touching the database, and `Model::delete()` evicts the entry it removes. The cache is a per request store, not a shared or persistent cache. For general purpose caching across requests, reach for [raxos/cache](/cache/).

## The query logger

Each connection has a logger. It is disabled by default; enable it to record a `QueryEvent` for every executed statement and an `EagerLoadEvent` for every relation batch that gets loaded.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Db;

$connection = Db::getOrFail();
$connection->logger->enable();

User::all();

echo $connection->logger->count() . ' queries recorded';
```

`Logger::print()` renders a self contained HTML report of every recorded event, including totals for query count, eager loads and execution time. Pass `true` to include a backtrace per event.

```php
$report = $connection->logger->print(backtrace: true);
```

Disable logging again with `Logger::disable()` when you no longer need it. Because logging keeps every event in memory, enable it only for the request or command you are inspecting.
