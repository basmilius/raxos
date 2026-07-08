---
outline: deep
---

# Documenting endpoints

A route only appears in the specification once its controller method carries an [#[Endpoint]](/openapi/api/Endpoint) attribute. That attribute describes the operation, and a handful of companion attributes fill in the details that reflection cannot infer.

## The #[Endpoint] attribute

`#[Endpoint]` documents a single operation: its summary, description, tags, security requirements, request model and possible responses. It is a method attribute placed alongside the route attribute.

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\Router\Attribute\{Controller, Get};

#[Controller('/me')]
final readonly class MeController
{
    #[OpenAPI\Endpoint(
        summary: 'Returns the current user.',
        tags: ['Me'],
        security: ['bearer'],
        responses: [
            new OpenAPI\Response(HttpResponseCode::OK, model: User::class),
            new OpenAPI\Response(HttpResponseCode::UNAUTHORIZED, model: ErrorResponse::class)
        ]
    )]
    #[Get]
    public function get(User $user): User
    {
        return $user;
    }
}
```

The `#[Endpoint]` attribute adds a summary, a tag, a security requirement and the possible responses. Path parameters and `#[MapQuery]` parameters are still discovered automatically by [RouterBuilder](/openapi/api/RouterBuilder), so you rarely list them by hand.

## Describing responses

Each response is a repeatable [#[Response]](/openapi/api/Response) attribute (or an entry in the `responses` array on `#[Endpoint]`, as above). A response maps an `HttpResponseCode` from [raxos/http](/http/) to a description and an optional model class.

```php
#[OpenAPI\Endpoint(summary: 'Lists the products.')]
#[OpenAPI\Response(HttpResponseCode::OK, model: ArrayListInterface::class, modelGeneric: Product::class)]
#[OpenAPI\Response(HttpResponseCode::FORBIDDEN, description: 'You may not list products.')]
#[Get]
public function index(): ArrayList
{
    // ...
}
```

When `model` is a builtin collection type, the `modelGeneric` argument names the item type. Two builtin collection types are recognised:

- `ArrayListInterface`: rendered as an array of the generic item.
- `Paginated`: rendered as an object with `items`, `page`, `page_size`, `pages` and `total`.

When `model` is any other `JsonSerializable` class, its schema is generated once and reused as a named component so the same response is not duplicated across operations.

## Parameters

Path parameters are inferred from the route pattern automatically. Query parameters mapped with the router `#[MapQuery]` attribute are added as optional query parameters without any extra work.

For parameters that reflection cannot see (an extra header, cookie or query value), declare a [#[Parameter]](/openapi/api/Parameter) inside `#[Endpoint(parameters: ...)]`.

```php
use Raxos\OpenAPI\Enum\In;

#[OpenAPI\Endpoint(
    summary: 'Searches products.',
    parameters: [
        new OpenAPI\Parameter('X-Region', In::HEADER, description: 'Region to search in.')
    ]
)]
#[Get]
public function search(): array
{
    // ...
}
```

Path parameters supplied here are ignored, because they are always taken from the route itself.

## Parameters from middleware

A router middleware that adds its own request parameters (a cursor middleware that reads `limit` and `offset`, for example) can describe those parameters itself instead of repeating them on every endpoint. Implement `ParameterizedMiddlewareInterface` from [contract](/contract/) alongside the router `MiddlewareInterface`, and [RouterBuilder](/openapi/api/RouterBuilder) picks up its parameters automatically for every route the middleware applies to.

```php
<?php
declare(strict_types=1);

use Attribute;
use Closure;
use Generator;
use Raxos\Contract\OpenAPI\ParameterizedMiddlewareInterface;
use Raxos\Contract\Router\MiddlewareInterface;
use Raxos\Http\{HttpRequest, HttpResponse};
use Raxos\OpenAPI\Definition\Parameter;
use Raxos\OpenAPI\Enum\In;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class Cursor implements MiddlewareInterface, ParameterizedMiddlewareInterface
{
    public function handle(HttpRequest $request, Closure $next): HttpResponse
    {
        // ...

        return $next($request);
    }

    public static function generateParameters(): Generator
    {
        yield 'limit' => new Parameter('limit', In::QUERY);
        yield 'offset' => new Parameter('offset', In::QUERY);
    }
}
```

`generateParameters()` yields `Raxos\OpenAPI\Definition\Parameter` instances, not the `#[Parameter]` attribute used above; it is a static method called once per route the middleware is attached to, independent of any particular request.

## Filter parameters

When an endpoint accepts the structured filters of a [raxos/search](/search/) enabled model, add a [#[FilterParams]](/openapi/api/FilterParams) attribute. It reflects the `#[Filter]` attributes declared on the given [raxos/database](/database/) model and adds one query parameter per structured filter.

```php
#[OpenAPI\Endpoint(summary: 'Lists the products.')]
#[OpenAPI\FilterParams(Product::class)]
#[Get]
public function index(): array
{
    // ...
}
```

Filter parameters that share a name with a parameter already present (for example a `#[MapQuery]` value) are skipped, so nothing is documented twice.

## Hiding a route

A [#[Hidden]](/openapi/api/Hidden) attribute on a controller or a single method excludes it from the generated specification. It works even when the method has an `#[Endpoint]` attribute.

```php
#[OpenAPI\Hidden]
#[Get('/internal')]
public function internal(): array
{
    // ...
}
```
