---
outline: deep
---

# Dynamic routing

`DynamicRouter` is a closure-based alternative to attribute-mapped controllers. Instead of deriving routes from class attributes, you register them one call at a time at runtime. It is handy for small scripts, health checks or tests where defining full controller classes is not worth it.

## Registering routes

Create a `DynamicRouter` and register a closure for each route. There is one method per HTTP verb: `get`, `post`, `put`, `patch`, `delete`, `options` and `head`. The generic `route` method takes an `HttpMethod` for anything else.

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;
use Raxos\Router\DynamicRouter;

$router = new DynamicRouter();
$router->get('/health', fn(): array => ['status' => 'ok']);
$router->post('/echo', fn(HttpRequest $request): array => $request->post->toArray());

$router->resolve(HttpRequest::create())->send();
```

A closure handler supports the same attribute-based parameter mapping as controller methods, so `#[MapQuery]`, `#[MapHeader]` and the other attributes work here too. See [Parameter mapping](/router/parameter-mapping).

```php
use Raxos\Router\Attribute\MapQuery;

$router->get('/todos', fn(#[MapQuery] int $page = 1): array => ['page' => $page]);
```

## Dependency injection

Pass a [container](/container/) to the constructor to resolve closure parameters that are not path, query or header values.

```php
use Raxos\Container\Container;
use Raxos\Router\DynamicRouter;

$container = new Container(production: true);
$router = new DynamicRouter($container);
```

## Compiling

Routes registered through the verb methods are compiled as you add them. If you build the route tables in another way and add routes outside that flow, call `compile` to rebuild the combined dynamic regular expressions before resolving requests.

```php
$router->compile();
```

`DynamicRouter` shares `resolve` and `path` with the main [Router](/router/api/Router) through the same underlying trait, so requests are matched and run in exactly the same way.
