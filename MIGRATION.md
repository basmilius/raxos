# Migration guide

## Preparing for 3.3.0

PHP 8.5 remains required. Raxos dependencies now use bounded minor-compatible constraints. Packages that use new capabilities require their 3.3 dependency; other sibling requirements start at 3.2. Upgrade the related packages together.

New capabilities use optional interfaces for scoped containers, managed transactions, keyset queries, atomic rate-limit snapshots, submission results and refresh rotation. Existing base interfaces gain no mandatory methods. Built-in implementations expose the new APIs; custom implementations can keep their previous behavior or opt in.

HTTP request builders expose a public `request()` method. A custom HttpClient subclass that overrides the old protected method must widen its visibility to public. Request-schema generation now follows input aliases and requiredness; regenerate client specifications and review conditional-rule diagnostics.

Queue policy, HTTP retries, Problem Details and OAuth modern profiles are explicit choices. Enabling refresh rotation requires an atomic persistence adapter with family replay detection; enabling a queue policy requires RabbitMQ support for confirmed publication and quorum retry queues. See their package guides for failure and duplicate-delivery behavior.

DateTime parsing rejects impossible ISO-shaped dates instead of normalizing them. `Every` now applies conjunctive search filters and weighted scores. Cached serialized false/null values are valid hits, and tagged values publish atomically with their indexes.

Library PHPDocs preserve the original `@since` on existing members. Only members introduced in this development round use 3.3.0. Tests and fixtures omit PHPDocs.

## Migrating to 3.2.0

Raxos 3.2.0 requires PHP 8.5. Update the Raxos libraries together because the collection, database, message-bus and OAuth contracts changed. The review findings and their implementation status are in [the code review](reports/codebase-review.html) and [the performance report](reports/performance-review.html).

## Message consumers must register their message classes

`MessageBus::createQueue()` now accepts `allowedClasses`, defaulting to an empty list. A queue with that default rejects serialized messages. Register the message classes that the worker accepts before upgrading it.

```php
$queue = $bus->createQueue(allowedClasses: [
    SendEmailMessage::class,
    GenerateReportMessage::class,
]);
```

The root serialized object must implement `MessageInterface`. Add any nested value-object classes to the same list if your message serializes objects instead of scalar values. Registration permits those objects' unserialization hooks; keep this list explicit. Malformed and unregistered messages are rejected without requeueing. Handler failures still requeue deliveries.

## OAuth authorization codes require S256 and atomic consumption

Update every implementation of `AuthorizationCodeInterface` and `TokenFactoryInterface`:

1. Implement `AuthorizationCodeInterface::getCodeChallenge(): ?string`.
2. Accept and persist the new final `?string $codeChallenge = null` argument to `saveAuthorizationCode()`.
3. Implement `consumeAuthorizationCode(ClientInterface $client, AuthorizationCodeInterface $authorizationCode): bool` as one atomic storage operation. It must consume only an unexpired code belonging to that client and return `true` for exactly one request.

For a relational store, a conditional delete can provide that guarantee:

```sql
DELETE FROM authorization_codes
WHERE token = :token
  AND client_id = :client_id
  AND expires_at > :now
```

Bind the requested client's ID and the code token. Return whether the affected row count is exactly one. A conditional update of a previously unused row is another option. A separate read followed by an unconditional delete does not prevent two requests from issuing tokens.

Authorization requests must include a valid `code_challenge` and `code_challenge_method=S256`. Token requests must include the corresponding `code_verifier`. Redirect URIs must match exactly; percent-encoding variants are no longer decoded into a match. Existing codes with a null challenge cannot be redeemed; start a new authorization flow. The grant consumes the code before issuing tokens. A failed token issuance therefore requires a new code.

## JWT verification has an explicit algorithm policy

`Jwt::decode($token, $keys)` permits HS256 by default. Pass an explicit list for other algorithms:

```php
$claims = Jwt::decode($token, [$publicKey], [JwtAlgorithm::RS256]);
```

An empty algorithm list is rejected. PEM keys cannot serve as HMAC secrets, including when an allowlist contains both HMAC and RSA algorithms. Multiple configured keys require a valid string `kid` identifying the configured key. Configure the algorithm list for the issuer being verified.

## Collection projections return generic collections

`chunk()`, `collapse()`, `column()`, `groupBy()`, `keys()`, `map()` and `only()` return `ArrayListInterface` rather than `static`. These operations can change the element type; a typed list can no longer contain incompatible projected values. Mutable inputs produce mutable generic lists and read-only inputs produce read-only generic lists. Group and chunk contents keep the original list type.

Update callers that require a concrete typed return class and custom implementations of `ArrayListInterface`. Value-preserving operations retain the typed list. Constructors, append/prepend, offset assignment and deserialization now enforce the declared element type.

## ORM cursors hydrate batches and release temporary identities

`QueryInterface::cursor()` and `StatementInterface::cursor()` have two additional arguments, `int $batchSize = 100` and `bool $retainCache = false`. Update custom implementations of these interfaces. Existing calls use batches of 100 and release identities first created for that batch. Pre-existing cached models, including dirty values, remain available.

```php
foreach (Order::select()->eagerLoad('items')->cursor(batchSize: 100) as $order) {
    // Process the order.
}
```

Use `retainCache: true` when later operations depend on identities discovered while streaming. That option retains model memory. Smaller batches trade more relation queries for less hydrated model memory. The cursor cannot control PDO driver buffering; MySQL may still buffer raw result rows.

Custom `CacheInterface` implementations must add `scope(callable $fn): mixed`. The scope returns the callback result, removes only identities introduced inside it, and cleans up on exceptions. Query count methods now preserve grouping, distinct and HAVING semantics; `totalCount()` excludes pagination, while `resultCount()` includes it. Scalar column results preserve zero, false, floats and null.

## Other behavior changes

- Use plain `RedisCache` with `RedisRateLimiterStore`. Tagged caches lack the atomic operations that the store needs and are rejected by its constructor.
- `HttpSendFile` defaults to zero throttle. Set a positive throttle explicitly if needed. Single byte ranges, suffixes and open-ended ranges are honored; invalid or multiple ranges return HTTP 416.
- Invalid JSON request bodies produce HTTP 400. Scalar JSON bodies, including `0`, are rejected as structured request data rather than mistaken for an empty body.
- Router preflight runs middleware and returns allowed methods without executing the target handler. Explicit OPTIONS handlers still run normally.
- HTTP request construction parses query parameters from an explicit URI. Headers are normalized consistently, and bearer-token matching is case insensitive.
- OpenAPI uses JSON Schema types and null unions, numeric exclusive bounds, and references for recursive DTOs. Regenerate client contracts and check client-generator output. Legacy boolean exclusive-bound arguments are translated using the corresponding minimum or maximum.
- Mail providers accept Raxos `Email` objects, and forced public-suffix reloads replace the list. Large PDF417 payloads fail instead of being truncated. Boarding-pass fields accept their documented single-value and array forms.
- Base64 preserves empty and zero strings. NanoID rejects nonpositive lengths. ULID rejects timestamps outside its 48-bit range and supports the Unix epoch. TOTP rejects empty secrets.
- Container tags distinguish an empty string and `"0"` from an untagged dependency. `#[Tag]` can be used on injected parameters. Circular resolution fails with a dependency exception and leaves the container available for a later request.
- New models omit unset columns so database defaults apply. Saving only relation changes executes pending relation writes. Ordered `HasOne` and many-to-many eager loads follow the same ordering as lazy loads. Zero-valued relation keys remain valid.
- Query groups retain their position inside WHERE and JOIN conditions and before ORDER BY or pagination. Native DATE_ADD/DATE_SUB intervals and GROUP_CONCAT string separators now use the SQL syntax required by MySQL and MariaDB.
- Structured search filters reject unsupported value types before changing the query. Numeric filters reject infinite and NaN inputs; SQLite range scoring uses floating-point division. Nested `Some` filters restore the outer filter's AND/OR mode after errors. `Every` remains unsupported.
- Refresh tokens must belong to the requesting OAuth client. Authorization response values are URL-encoded, and implicit tokens are returned only after successful persistence.
- OpenAPI preserves false, zero and empty examples. Query aliases use their public names, and method-specific query and middleware parameters stay on their own operation. Explicit model schemas take precedence over the `Stringable` fallback.

Members introduced in 3.2.0 use `@since 3.2.0`; changes to older members retain their original version. Parameter tags contain only the type and name. Tests and fixtures omit PHPDocs.
