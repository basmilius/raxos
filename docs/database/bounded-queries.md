# Transactions and bounded queries

These APIs are available on the built-in Raxos connections and queries in 3.3. Custom implementations can opt in through `TransactionalConnectionInterface` and `KeysetQueryInterface`; the original interfaces gain no mandatory methods.

## Managed transactions

`transactional()` returns the callback result and commits on success. A callback failure rolls back the transaction level owned by the operation. Nested calls use savepoints; a failed nested operation marks the enclosing transaction rollback-only even when the caller catches its exception.

```php
$result = $connection->transactional(function () use ($connection): int {
    $connection->query()->insertIntoValues('jobs', ['status' => 'pending'])->run();
    $id = $connection->lastInsertIdInteger();
    $connection->afterCommit(static function () use ($id): void {
        notifyWorker($id);
    });

    return $id;
});
```

Do not manually commit, roll back or open additional transaction levels inside a managed callback. `Db::transactional()` and `Db::afterCommit()` use the selected registered connection. They reject a custom connection without the optional capability.

`afterCommit()` runs immediately outside a transaction. Inside one, hooks run in registration order after the outer commit. Rollback discards them. Every hook runs even if an earlier hook throws; the first failure then propagates. A hook failure does not undo committed data, so make notification work retryable or use an application outbox for durable delivery.

## Forward pagination

```php
$page = $connection->query()
    ->select(['id', 'created_at', 'title'])
    ->from('articles')
    ->whereField('published', 1)
    ->cursorPaginate(
        size: 25,
        cursor: $requestCursor,
        columns: ['created_at', 'id'],
        descending: true
    );
```

`CursorPage` serializes as `items`, `next_cursor` and `has_more`. The query fetches at most `size + 1` rows and performs no COUNT or OFFSET query. Valid page sizes are 1 through 10,000. Existing ordering and limits are replaced on a clone; the original builder is unchanged.

Select every sort column. Each must contain a non-null scalar value, and the final column must make the ordering unique. All columns use the chosen direction. Keep sort keys stable while processing pages. Add an index matching the filters and ordered columns for efficient seeks.

Cursors are bound to the query, bindings, model and order. A different filter or direction rejects a cursor. They are opaque continuation values, not signed authorization tokens. Apply authorization filters on every request. GROUP BY, HAVING and UNION require an explicit outer keyset query. An unsigned cursor does not prevent a caller from changing its position.

## Batch processing

```php
foreach (Order::select()->eagerLoad('items')->lazyById(batchSize: 128) as $order) {
    processOrder($order);
}

Order::select()->chunkById(function ($orders): bool {
    processBatch($orders);
    return true;
}, batchSize: 128);
```

`lazyById()` closes each bounded result before relation queries run. It releases identities first introduced by a batch while preserving identities cached before the scope. Holding returned models yourself still retains their memory. `retainCache: true` deliberately keeps discovered identities. Returning `false` from `chunkById()` stops before another page is requested.

A normal `cursor()` can still depend on PDO buffering. Keyset iteration bounds each SQL result and supports eager loading with unbuffered MySQL and MariaDB connections. Query logging retains events when enabled; disable it during large jobs if those events should not accumulate.
