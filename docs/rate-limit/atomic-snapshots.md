# Atomic rate-limit snapshots

`RedisRateLimiterStore` implements the optional `RateLimiterSnapshotStoreInterface`. A snapshot reads the count and remaining lifetime together and can record an attempt in the same Lua operation.

```php
$snapshot = $store->snapshot('catalog', interval: 60);
$readOnly = $store->snapshot('catalog', interval: 60, increment: false);
```

The result has integer `operations` and `ttl` fields. The interval must be positive and uses seconds. The first increment starts expiry; later increments do not extend it. Remaining milliseconds round up to seconds so a live key does not appear expired. A missing or permanent key reports zero remaining TTL.

`RateLimiter` prefers this capability when supplied and retains its existing fallback for custom stores implementing only `RateLimiterStoreInterface`. Native Redis snapshots require one evaluation rather than separate count and TTL reads.
