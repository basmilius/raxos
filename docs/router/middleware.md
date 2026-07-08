---
outline: deep
---

# Middleware and validation

Middleware runs before a route handler and can short-circuit the request, for example to reject an unauthenticated call. In Raxos Router a middleware is an attribute that implements `MiddlewareInterface` from [contract](/contract/).

## Writing middleware

A middleware class implements a single `handle` method. It receives the request and a `$next` closure. Call `$next($request)` to continue the pipeline, or return an `HttpResponse` directly to stop it.

```php
<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Attribute;
use Closure;
use Raxos\Contract\Router\MiddlewareInterface;
use Raxos\Http\{HttpRequest, HttpResponse};
use Raxos\Router\Responds;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class IsAuthenticated implements MiddlewareInterface
{
    use Responds;

    public function handle(HttpRequest $request, Closure $next): HttpResponse
    {
        if ($request->bearerToken() === null) {
            return $this->forbidden();
        }

        return $next($request);
    }
}
```

Because a middleware is an attribute, you declare the `#[Attribute]` targets it supports. Targeting `TARGET_CLASS` lets it guard a whole controller, and `TARGET_METHOD` lets it guard a single route.

## Applying middleware

Place the middleware attribute on a controller class to apply it to every route in that controller, or on a single method to apply it to just that route. Controller-level middleware runs before route-level middleware.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Middleware\{IsAdmin, IsAuthenticated};
use Raxos\Router\Attribute\{Controller, Delete, Get};

#[Controller('/todos')]
#[IsAuthenticated]
final readonly class TodoController
{
    #[Get('/')]
    public function index(): array
    {
        return ['todos' => []];
    }

    #[Delete('/$id')]
    #[IsAdmin]
    public function destroy(int $id): array
    {
        return ['deleted' => $id];
    }
}
```

Middleware classes can use the `#[Injected]` attribute for properties and the `Responds` trait for response helpers, exactly like controllers. See [Parameter mapping](/router/parameter-mapping) and [Building responses](/router/responses).

## Validating the request body

The `#[Validated]` attribute validates the request body against a request model class that implements `HttpRequestModelInterface` from [http](/http/), then injects the validated model. It accepts both JSON bodies and form data, including uploaded files.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\CreateTodoRequest;
use Raxos\Router\Attribute\{Controller, Post, Validated};

#[Controller('/todos')]
final readonly class TodoController
{
    #[Post('/')]
    public function store(#[Validated] CreateTodoRequest $body): array
    {
        return ['title' => $body->title];
    }
}
```

## Validating the query string

The `#[ValidatedQuery]` attribute works the same way but validates the query string instead of the body, again into a request model.

```php
use App\Http\Request\TodoFilterRequest;
use Raxos\Router\Attribute\{Get, ValidatedQuery};

#[Get('/')]
public function index(#[ValidatedQuery] TodoFilterRequest $filter): array
{
    return ['status' => $filter->status];
}
```

When validation fails, both attributes throw a `ValidationFailedException`. This is one of the runtime exceptions the router can raise, all of which implement `RuntimeExceptionInterface` from [contract](/contract/). Catch it at the boundary of your application to render a validation error response.
