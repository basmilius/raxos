---
outline: deep

cards:
    highlights:
        -   title: Router
            code: true
            details: 'The attribute-based router that compiles controllers into static and dynamic route tables.'
            link: /router/api/Router
        -   title: DynamicRouter
            code: true
            details: 'A closure-based alternative for registering routes one call at a time.'
            link: /router/api/DynamicRouter
        -   title: Attributes
            code: true
            details: 'Controller, Get, Post and the parameter-mapping attributes that describe your routes.'
            link: /router/api/Attributes
        -   title: Responds
            code: true
            details: 'A trait with short helpers for building common HttpResponse instances.'
            link: /router/api/Responds
---

# Router

Raxos Router is a fast, attribute-based router for PHP 8.5. Controllers are plain classes annotated with attributes such as `#[Controller]` and `#[Get]`. The router reflects them once into a compiled set of static and dynamic routes, then resolves an [HttpRequest](/http/) into an [HttpResponse](/http/) by running a per-route pipeline of middleware and the target method. Path, query and header values, ORM models, and [container](/container/) services are all injected automatically based on parameter types and mapping attributes. A closure-based `DynamicRouter` offers a lighter alternative when full controller classes are not needed.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Routing basics](/router/routing-basics): controllers, route attributes, path parameters and nested controllers.
- [Parameter mapping](/router/parameter-mapping): how values are injected into constructors, methods and properties.
- [Middleware and validation](/router/middleware): the middleware pipeline and the request validation attributes.
- [Building responses](/router/responses): how return values become responses and the `Responds` helpers.
- [Dynamic routing](/router/dynamic-routing): register routes as closures with `DynamicRouter`.
- [Error handling](/router/error-handling): the router exception hierarchy and how to catch mapping errors and runtime errors.

## Quick example

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Raxos\Router\Attribute\{Controller, Get};

#[Controller('/todos')]
final readonly class TodoController
{
    #[Get('/')]
    public function index(): array
    {
        return ['todos' => []];
    }

    #[Get('/$id')]
    public function show(int $id): array
    {
        return ['id' => $id];
    }
}
```

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

$router->resolve(HttpRequest::create())->send();
```

## Installation

Install it with Composer.

```shell
composer require raxos/router
```

See [installation](/router/installation) for requirements, or use the sidebar to navigate this package.
