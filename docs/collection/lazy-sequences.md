# Lazy sequences and cursor pages

`LazySequence` evaluates values during iteration. Existing `ArrayList` operations remain eager.

```php
use Raxos\Collection\LazySequence;

$sequence = LazySequence::from(static function (): Generator {
    foreach (readRows() as $row) {
        yield $row;
    }
});

foreach ($sequence->filter(static fn(array $row): bool => $row['enabled'])->take(10) as $row) {
    processRow($row);
}
```

Construction does not open the source. `map()` and `filter()` pass both value and source key and retain keys. `take()` stops without reading an extra source value. `chunk()` buffers one batch and emits the final partial batch. `each()` consumes immediately. `toArray()` materializes the result; pass `false` to retain values with duplicate source keys instead of overwriting them.

Arrays and IteratorAggregate inputs can be iterated repeatedly. Factories must return a fresh iterable for each iteration. An Iterator or Generator supplied directly is one-shot; reuse fails explicitly, including after partial iteration. Derived sequences keep their source's lifetime restrictions. `take(0)` does not consume the source.

`CursorPage` is a forward-pagination result from the database library. It exposes `items`, `nextCursor` and `hasMore`, serialized as `items`, `next_cursor` and `has_more`. It intentionally carries no total or page count. See [bounded queries](/database/bounded-queries).
