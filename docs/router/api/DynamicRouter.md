---
outline: deep
---

# DynamicRouter

`Raxos\Router\DynamicRouter` is a closure-based alternative to controller mapping. Routes are registered one by one at runtime instead of being derived from attributes.

```php
class DynamicRouter implements RouterInterface
{
    use Resolvable;

    public function __construct(
        public ?ContainerInterface $container = null
    );
}
```

The optional container is used to resolve closure dependencies that are not path, query or header values.

## Methods

### get, post, put, patch, delete, options, head

```php
public function get(string $path, callable $handler): void
public function post(string $path, callable $handler): void
public function put(string $path, callable $handler): void
public function patch(string $path, callable $handler): void
public function delete(string $path, callable $handler): void
public function options(string $path, callable $handler): void
public function head(string $path, callable $handler): void
```

Registers a route for the corresponding HTTP method. Each delegates to `route`.

### route

```php
public function route(HttpMethod $method, string $path, callable $handler): void
```

Registers a route for an arbitrary HTTP method. It reflects the handler closure to build its injectable parameters and any middleware, then stores it as a static or dynamic route depending on whether the path has parameters.

### compile

```php
public function compile(): void
```

Rebuilds the combined dynamic regular expressions. Call it after adding routes outside the normal verb-method flow.

### resolve

```php
public function resolve(HttpRequest $request): HttpResponse
```

Matches the request and runs its frame stack. Provided by the `Resolvable` trait, shared with [Router](/router/api/Router).

### path

```php
public function path(array $handler): string
```

Looks up the raw path for a `[class, method]` handler pair. Provided by the `Resolvable` trait.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;
use Raxos\Router\DynamicRouter;

$router = new DynamicRouter();
$router->get('/health', fn(): array => ['status' => 'ok']);

$router->resolve(HttpRequest::create())->send();
```

See [Dynamic routing](/router/dynamic-routing) for more.
