# Lookups and cached computation

## Stored false and null

```php
use Raxos\Cache\Redis\RedisCache;

$cache = new RedisCache('app');
$entry = $cache->lookup('app:settings');

if ($entry->found) {
    useSettings($entry->value);
}
```

`CacheEntry::found` distinguishes absence from a stored false or null. Native Redis values use one atomic Lua snapshot, decoded with the configured phpredis serializer and compression. A serializer override on `get()` keeps its existing EXISTS/GET path. Without a serializer, Redis values retain the string representation produced by phpredis; lookup does not introduce a new value codec.

`remember()` uses this distinction, so serialized false and null are valid cache hits. Native tagged-cache hits also use one lookup. Custom tagged `get()` or `exists()` overrides retain their previous read path.

## One overlapping computation

```php
$value = $cache->rememberLocked(
    key: 'app:catalog',
    ttl: 60,
    fn: static fn(): array => loadCatalog(),
    lockTtl: 30,
    waitTimeout: 2.0
);
```

The lock lease uses seconds; waiting is bounded by `waitTimeout`. The factory runs only after acquiring a unique owner lease and checking the cache again. Staging uses the existing `setex()` codec. Publication and release check the owner atomically, so a factory that outlives its lease cannot overwrite a newer owner.

A failed factory releases its lease. Cleanup attempts both staging deletion and release and preserves the original factory failure. A timed-out waiter or lost lease raises a cache exception. Set the lease above the expected computation duration. There is no automatic lease extension, and another owner can start computing after expiry. Make side effects idempotent.

## Tagged writes

Tagged writes stage the value, then atomically publish it with every tag membership and expiry. Shorter writes never shorten a longer tag-index lifetime; permanent indexes remain permanent. A flush overlapping staging cannot leave a published value outside its tag indexes.

Staging, publication and cleanup require separate Redis operations. These multi-key scripts support a single Redis server, not Redis Cluster hash-slot routing.
