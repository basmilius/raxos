---
outline: deep
---

# Parameter mapping

The router fills the parameters of controller constructors, route methods and middleware automatically. It also fills public properties marked with the `#[Injected]` attribute. The value for each injectable is resolved in a fixed order.

## Resolution order

For every parameter or injected property, the router tries the following sources in order and uses the first one that applies:

1. A cached value from a value provider that already ran during this request.
2. A value provider attribute on the parameter, such as `#[MapQuery]`, `#[MapHeader]`, `#[MapModel]`, `#[MapModelRelation]`, `#[Validated]` or `#[ValidatedQuery]`.
3. A router global (for example the current `request` or `router`).
4. A request parameter, including matched path parameters.
5. The parameter's declared default value.
6. A dependency resolved from the [container](/container/), by the parameter's type.

If none of these apply, the router throws an exception describing the missing injection.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Service\TodoRepository;
use Raxos\Http\HttpRequest;
use Raxos\Router\Attribute\{Controller, Get};

#[Controller('/todos')]
final readonly class TodoController
{
    public function __construct(
        private TodoRepository $todos
    ) {}

    #[Get('/$id')]
    public function show(HttpRequest $request, int $id): array
    {
        return $this->todos->find($id);
    }
}
```

Here `$todos` is resolved from the container, `$request` is a router global, and `$id` is a matched path parameter.

## Mapping query values

The `#[MapQuery]` attribute reads a value from the request query string. By default it uses the parameter name as the key. Pass a `key` to read a different query key, and an `enum` to cast array values to a backed enum.

```php
use Raxos\Router\Attribute\{Get, MapQuery};

#[Get('/')]
public function index(
    #[MapQuery] int $page = 1,
    #[MapQuery('q')] ?string $search = null
): array {
    return ['page' => $page, 'search' => $search];
}
```

A query value is converted to the parameter's type. An `array` parameter always receives an array, even when the query holds a single value. With the `enum` argument, each array item is cast to the given backed enum and invalid items are dropped.

## Mapping headers

The `#[MapHeader]` attribute reads a request header. When the header is missing, the parameter's default value is used.

```php
use Raxos\Router\Attribute\{Get, MapHeader};

#[Get('/')]
public function index(
    #[MapHeader('Accept-Language')] string $language = 'en'
): array {
    return ['language' => $language];
}
```

## Mapping models

The `#[MapModel]` attribute resolves an ORM model from [database](/database/) directly from a path parameter, loading it by its primary key. If no model matches, the underlying `singleOrFail` call fails and the router reports the error.

```php
use App\Model\Todo;
use Raxos\Router\Attribute\{Controller, Get, MapModel};

#[Controller('/todos')]
final readonly class TodoController
{
    #[Get('/$id')]
    public function show(#[MapModel] Todo $todo): array
    {
        return $todo->jsonSerialize();
    }
}
```

The `#[MapModelRelation]` attribute resolves a related model through a relation on an already resolved parent model. It takes the name of the parent parameter and the relation property to query, then filters that relation by the path primary key.

```php
use App\Model\{Todo, TodoComment};
use Raxos\Router\Attribute\{Controller, Get, MapModel, MapModelRelation};

#[Controller('/todos/$todo/comments')]
final readonly class TodoCommentController
{
    #[Get('/$comment')]
    public function show(
        #[MapModel] Todo $todo,
        #[MapModelRelation('todo', 'comments')] TodoComment $comment
    ): array {
        return $comment->jsonSerialize();
    }
}
```

::: info
`#[MapModel]` and `#[MapModelRelation]` require the [database](/database/) package. It is an optional dependency of the router.
:::

## Injected properties

The `#[Injected]` attribute marks a public property on a controller or middleware to be filled the same way as method parameters, using the same resolution order.

```php
use Raxos\Http\HttpRequest;
use Raxos\Router\Attribute\{Controller, Get, Injected};

#[Controller('/todos')]
final class TodoController
{
    #[Injected]
    public HttpRequest $request;

    #[Get('/')]
    public function index(): array
    {
        return ['path' => $this->request->pathName];
    }
}
```

## Writing a custom value provider

The `#[MapQuery]`, `#[MapHeader]`, `#[MapModel]` and `#[MapModelRelation]` attributes are all built on the same small contract. You can add your own `#[MapXxx]` style attribute by implementing two interfaces from [contract](/contract/): `AttributeInterface`, which marks the class as a router attribute, and `ValueProviderInterface`, which supplies the value.

`ValueProviderInterface` has two methods, both receiving the `Injectable` definition for the parameter being filled:

- `getRegex(Injectable $injectable): string` returns the path regex fragment for this parameter. Providers that read from the query string or headers reuse `RouterUtil::regex` (or the higher level `RouterUtil::convertPathParam`) to build it.
- `getValue(HttpRequest $request, Injectable $injectable): mixed` returns the actual value for the current request. The `Injectable` gives you `name`, `types`, `primaryType` and a `defaultValue` you can fall back to.

The example below resolves the authenticated merchant user from the request and injects it wherever the attribute appears.

```php
<?php
declare(strict_types=1);

namespace App\Http\Attribute;

use App\Model\MerchantUser;
use Attribute;
use Raxos\Contract\Router\{AttributeInterface, ValueProviderInterface};
use Raxos\Http\HttpRequest;
use Raxos\Router\Definition\Injectable;
use Raxos\Router\RouterUtil;

#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class MapMerchantUser implements AttributeInterface, ValueProviderInterface
{
    public function getRegex(Injectable $injectable): string
    {
        return RouterUtil::regex('[^/]+', $injectable->name, $injectable->defaultValue->defined);
    }

    public function getValue(HttpRequest $request, Injectable $injectable): MerchantUser
    {
        return $request->parameters->get('merchantUser');
    }
}
```

Use it exactly like the built in attributes:

```php
use App\Http\Attribute\MapMerchantUser;
use App\Model\MerchantUser;
use Raxos\Router\Attribute\{Controller, Get};

#[Controller('/merchant')]
final readonly class MerchantController
{
    #[Get('/profile')]
    public function profile(#[MapMerchantUser] MerchantUser $user): array
    {
        return $user->jsonSerialize();
    }
}
```

::: tip
A provider that does not read from the path (for example one that reads a header or a global) still needs `getRegex` to return a valid fragment. Delegate to `RouterUtil::regex` with a simple pattern such as `[^/]+`, as the built in `#[MapHeader]` does.
:::

## Router globals

The router keeps a small pool of shared values in a public `globals` property, a `Map` from [collection](/collection/). Anything with access to the router can read from it, and this pool is what the resolution order refers to as a "router global".

Two entries are managed for you:

- The router sets `router` to itself at construction, on both `Router` and `DynamicRouter`.
- `Runner` sets `request` to the active `HttpRequest` at the start of each run and updates the current `frame` as it walks the frame stack.

Application code can add its own globals so that any parameter or injected property can receive them by name. Middleware is the natural place to do this, since it runs before the controller.

```php
<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Service\Tenant;
use Attribute;
use Closure;
use Raxos\Contract\Router\MiddlewareInterface;
use Raxos\Http\{HttpRequest, HttpResponse};
use Raxos\Router\Attribute\Injected;
use Raxos\Router\Router;

#[Attribute(Attribute::TARGET_CLASS)]
final class ResolveTenantMiddleware implements MiddlewareInterface
{
    #[Injected]
    public Router $router;

    public function handle(HttpRequest $request, Closure $next): HttpResponse
    {
        $this->router->globals->set('tenant', Tenant::fromRequest($request));

        return $next($request);
    }
}
```

The middleware receives the router through an `#[Injected]` property, resolved from the `router` global. After it runs, a `Tenant $tenant` parameter on any downstream controller method resolves from the global, and a helper such as a global `request()` function can read `$router->globals->get('request')` from anywhere that holds the router.

