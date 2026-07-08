---
outline: deep
---

# Attributes

The routing and parameter-mapping attributes live in the `Raxos\Router\Attribute` namespace. They describe controllers, routes, and how values are injected into parameters and properties.

## Controller

```php
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
readonly class Controller implements AttributeInterface
{
    public function __construct(public string $prefix = '/');
}
```

Marks a class as a controller and sets its path prefix. Children and routes are relative to this prefix. The prefix may contain a path parameter such as `$id`.

## Child

```php
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class Child implements AttributeInterface
{
    public function __construct(public string $controller);
}
```

Nests another controller class under the current one, inheriting and extending its path prefix. The argument is the class-string of the child controller. Repeatable.

## Route attributes

```php
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Get extends AbstractRoute
{
    public function __construct(string $path = '/');
}
```

`Get`, `Post`, `Put`, `Patch`, `Delete`, `Head`, `Options` and `Any` all extend `AbstractRoute` and share the same constructor. Each registers a route for its HTTP method. `Any` matches every method without a more specific route. They are repeatable, so a single method can answer several methods or paths. The path is relative to the controller prefix and may contain path parameters such as `$id`.

```php
use Raxos\Router\Attribute\{Get, Post};

#[Get('/')]
#[Post('/')]
public function indexOrStore(): array
{
    return [];
}
```

## Injected

```php
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Injected implements AttributeInterface {}
```

Marks a public property on a controller or middleware to be filled by the injector, using the same resolution order as method parameters.

## MapHeader

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class MapHeader implements AttributeInterface, ValueProviderInterface
{
    public function __construct(public string $header);
}
```

Maps a parameter to a request header value. Returns the parameter's default value when the header is missing.

## MapQuery

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class MapQuery implements AttributeInterface, ValueProviderInterface
{
    public function __construct(public ?string $key = null, public ?string $enum = null);
}
```

Maps a parameter to a query-string value. Pass `key` to read a different query key than the parameter name, and `enum` to cast array values to a backed enum. The value is converted to the parameter type, an array, or a backed enum as needed.

## MapModel

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class MapModel implements AttributeInterface, ValueProviderInterface
{
    public function getValue(HttpRequest $request, Injectable $injectable): ?Model;
}
```

Maps a parameter to an ORM model, loaded by its primary key from the matching path value. Requires the [database](/database/) package. Throws when no model matches.

## MapModelRelation

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class MapModelRelation implements AttributeInterface, ValueProviderInterface
{
    public function __construct(public string $parentInstanceName, public string $relationKey);
}
```

Maps a parameter to a related model, looked up through a relation on an already resolved parent model. `parentInstanceName` is the name of the parent model parameter and `relationKey` is the relation property to query. Requires the [database](/database/) package.

## Validated

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class Validated implements AttributeInterface, ValueProviderInterface
{
    public function getValue(HttpRequest $request, Injectable $injectable): HttpRequestModelInterface;
}
```

Validates the request body (JSON or form data, including files) against an `HttpRequestModelInterface` class and injects the validated model. Throws a `ValidationFailedException` on failure.

## ValidatedQuery

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class ValidatedQuery implements AttributeInterface, ValueProviderInterface
{
    public function getValue(HttpRequest $request, Injectable $injectable): HttpRequestModelInterface;
}
```

Validates the request query string against an `HttpRequestModelInterface` class and injects the validated model. Throws a `ValidationFailedException` on failure.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\CreateTodoRequest;
use Raxos\Router\Attribute\{Controller, Get, MapQuery, Post, Validated};

#[Controller('/todos')]
final readonly class TodoController
{
    #[Get('/')]
    public function index(#[MapQuery] int $page = 1): array
    {
        return ['page' => $page];
    }

    #[Post('/')]
    public function store(#[Validated] CreateTodoRequest $body): array
    {
        return ['title' => $body->title];
    }
}
```

See [Parameter mapping](/router/parameter-mapping) and [Middleware and validation](/router/middleware) for usage in context.
