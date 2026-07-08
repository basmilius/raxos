---
outline: deep
---

# MiddlewareInterface (Router)

`Raxos\Contract\Router\MiddlewareInterface` is an extension point for router middleware in [raxos/router](/router/). Implementations sit in the request pipeline and decide whether to short circuit with their own response or forward the request to the next handler.

## Signature

```php
interface MiddlewareInterface
{
    public function handle(HttpRequest $request, Closure $next): HttpResponse;
}
```

The `$next` parameter is typed as `Closure(HttpRequest):HttpResponse`, and `handle` may throw any `Throwable`.

## Methods

### `handle(HttpRequest $request, Closure $next): HttpResponse`

Handles the current request. An implementation can either return its own `HttpResponse` to short circuit the pipeline, or call `$next($request)` to continue to the next middleware or the controller.

## Notes

- The `HttpRequest` and `HttpResponse` types come from [raxos/http](/http/).
- Middleware is registered on routes or controllers in raxos/router, typically through attributes.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Middleware;

use Attribute;
use Closure;
use Override;
use Raxos\Contract\Router\MiddlewareInterface;
use Raxos\Database\Db;
use Raxos\Http\{HttpRequest, HttpResponse};

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class WithTransaction implements MiddlewareInterface
{
    #[Override]
    public function handle(HttpRequest $request, Closure $next): HttpResponse
    {
        Db::transaction();

        $response = $next($request);

        Db::commit();

        return $response;
    }
}
```

The middleware is both an attribute and an implementation of `MiddlewareInterface`, so it can be attached to a controller method with `#[WithTransaction]` in raxos/router.
