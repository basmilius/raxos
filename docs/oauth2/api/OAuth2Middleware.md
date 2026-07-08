---
outline: deep
---

# OAuth2Middleware

`Raxos\OAuth2\Server\OAuth2Middleware`

Router middleware that authenticates a request using a bearer access token before it reaches the route handler. It implements `Raxos\Contract\Router\MiddlewareInterface` and uses the `Responds` trait from [router](/router/) to build error responses.

## Signature

```php
abstract readonly class OAuth2Middleware implements MiddlewareInterface
{
    use Responds;

    public function __construct(
        protected OAuth2Server $oAuth2
    ) {}

    public function handle(HttpRequest $request, Closure $next): HttpResponse;
}
```

## Methods

### `__construct(OAuth2Server $oAuth2)`

Creates the middleware for the given server.

### `handle(HttpRequest $request, Closure $next): HttpResponse`

Validates the bearer token, its owning client and its expiry, then calls `$next($request)`. It rejects the request with:

- an `InvalidRequestException` when the `Authorization: Bearer` header is missing,
- an `InvalidTokenException` when the token is unknown or has expired,
- an `InvalidClientException` when the owning client no longer exists.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Raxos\OAuth2\Server\OAuth2Middleware as BaseOAuth2Middleware;

final readonly class OAuth2Middleware extends BaseOAuth2Middleware {}
```

Attach the resulting class to a route or controller group in the router. See [Protecting routes](/oauth2/middleware) for a full walkthrough.
