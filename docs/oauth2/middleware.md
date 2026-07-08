---
outline: deep
---

# Protecting routes

`OAuth2Middleware` guards resource routes by validating a bearer access token before the request reaches the handler. It implements `Raxos\Contract\Router\MiddlewareInterface`, so it can be attached to any [router](/router/) route or group.

## Defining the middleware

The base middleware is abstract but complete: extending it is enough to get a working guard.

```php
<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Raxos\OAuth2\Server\OAuth2Middleware as BaseOAuth2Middleware;

final readonly class OAuth2Middleware extends BaseOAuth2Middleware {}
```

The constructor takes the `OAuth2Server` instance, which the router resolves from the container when it builds the middleware.

## Attaching it to a route

Attach the resulting middleware to a route or controller group in the router. Every request that reaches the guarded handler is then guaranteed to carry a valid, unexpired bearer token.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Middleware\OAuth2Middleware;
use Raxos\Http\HttpRequest;
use Raxos\Router\Attribute\{Controller, Get, Middleware};

#[Controller('/api')]
#[Middleware(OAuth2Middleware::class)]
final readonly class ApiController
{
    #[Get('/me')]
    public function me(HttpRequest $request): array
    {
        return ['ok' => true];
    }
}
```

## What it validates

The `handle()` method runs before the next handler in the pipeline and performs these checks:

1. The request must carry an `Authorization: Bearer <token>` header. When it is missing or does not start with `Bearer `, the middleware responds with an [InvalidRequestException](/oauth2/errors).
2. The token is resolved through `TokenFactoryInterface::getAccessToken()`. When it is unknown, the middleware responds with an [InvalidTokenException](/oauth2/errors).
3. When the token is expired (`isExpired()` returns `true`), the middleware responds with an [InvalidTokenException](/oauth2/errors) that reports the token has expired.
4. The owning client is resolved through `ClientFactoryInterface::getClient()`. When it no longer exists, the middleware responds with an [InvalidClientException](/oauth2/errors).

When all checks pass, the request is passed to the next handler in the pipeline. The error responses are built through the `Responds` trait, so a failing check produces a JSON error body rather than throwing.
