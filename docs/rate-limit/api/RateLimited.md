---
outline: deep
---

# RateLimited

`Raxos\RateLimit\Router\RateLimited`

An abstract router middleware that enforces a [`Rate`](/rate-limit/api/Rate) for incoming requests
and adds the standard `ratelimit-*` response headers. It implements the `MiddlewareInterface` from
[raxos/router](/router/) and works with the `HttpRequest` and `HttpResponse` objects from
[raxos/http](/http/). See the [router middleware](/rate-limit/router-middleware) guide for a full
walkthrough.

```php
abstract readonly class RateLimited implements MiddlewareInterface
{
    public RateLimiter $rateLimiter;

    public function __construct(Rate $rate, RateLimiterStoreInterface $store);

    public function handle(HttpRequest $request, Closure $next): HttpResponse;

    abstract protected function getKey(): string;
    abstract protected function getResponse(RateLimitStatus $status): HttpResponse;
}
```

## Constructor

### `__construct(Rate $rate, RateLimiterStoreInterface $store)`

Builds the internal `RateLimiter` from the given rate and store, exposed as the public readonly
`rateLimiter` property.

## Methods

### `handle(HttpRequest $request, Closure $next): HttpResponse`

Checks the status for the current key. When the limit is exceeded it short circuits to `getResponse`
instead of calling `$next`. It always adds `ratelimit-limit`, `ratelimit-remaining` and
`ratelimit-reset` headers, and adds `retry-after` when the caller is over the limit.

### `abstract protected getKey(): string`

Returns the unique identifier for the current caller. Implemented by subclasses, for example from an
IP address, a user id or an API token.

### `abstract protected getResponse(RateLimitStatus $status): HttpResponse`

Returns the response to send when the caller is rate limited. Implemented by subclasses, typically a
429 Too Many Requests response.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpResponse;
use Raxos\RateLimit\Router\RateLimited;
use Raxos\RateLimit\RateLimitStatus;

final readonly class ThrottleByIp extends RateLimited
{

    protected function getKey(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    protected function getResponse(RateLimitStatus $status): HttpResponse
    {
        // Build and return a 429 response using raxos/http.
    }

}
```

## See also

- [Router middleware](/rate-limit/router-middleware)
- [RateLimiter](/rate-limit/api/RateLimiter)
- [RateLimitStatus](/rate-limit/api/RateLimitStatus)
