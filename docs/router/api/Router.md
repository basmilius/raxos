---
outline: deep
---

# Router

`Raxos\Router\Router` is the main attribute-based router. It holds the compiled static and dynamic route tables and resolves an incoming request into a response.

```php
readonly class Router implements RouterInterface
{
    use Resolvable;

    public function __construct(
        public ?ContainerInterface $container,
        public array $dynamicRoutes = [],
        public array $staticRoutes = []
    );
}
```

The constructor takes an already compiled route mapping. In practice you use one of the static factory methods instead of constructing it directly.

## Methods

### createFromControllers

```php
public static function createFromControllers(?ContainerInterface $container, array $controllers): self
```

Builds a router by mapping the given controller classes. The mapping (reflecting attributes into route tables) runs once. The optional container resolves controller dependencies that are not path, query or header values.

### createFromMapping

```php
public static function createFromMapping(?ContainerInterface $container, array $dynamicRoutes, array $staticRoutes): self
```

Builds a router from a previously computed or cached mapping, skipping the mapping step. Use this when you have serialized or precomputed the route tables, for example in a build step.

### resolve

```php
public function resolve(HttpRequest $request): HttpResponse
```

Matches the request against the static and dynamic route tables and runs the matching frame stack of middleware and the target handler. Provided by the `Resolvable` trait. Returns a not-found response when no route matches, and a method-not-allowed response when a path matches but the method does not.

### path

```php
public function path(array $handler): string
```

Looks up the raw path for a `[class, method]` handler pair. Provided by the `Resolvable` trait. Throws when the handler is not a mapped route.

## Example

```php
<?php
declare(strict_types=1);

use App\Http\Controller\TodoController;
use Raxos\Container\Container;
use Raxos\Http\HttpRequest;
use Raxos\Router\Router;

$container = new Container(production: true);
$router = Router::createFromControllers($container, [
    TodoController::class,
]);

$response = $router->resolve(HttpRequest::create());
$response->send();
```

See [Routing basics](/router/routing-basics) for the full flow.
