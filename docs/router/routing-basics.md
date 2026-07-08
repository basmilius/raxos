---
outline: deep
---

# Routing basics

A controller is a plain class annotated with the `#[Controller]` attribute. Each public method that handles a request is annotated with a route attribute such as `#[Get]` or `#[Post]`. The router reads these attributes once, at mapping time, and compiles them into fast lookup tables.

## Controllers and route methods

The `#[Controller]` attribute sets a path prefix. Every route in the class is relative to that prefix. The route attributes are `#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`, `#[Head]`, `#[Options]` and `#[Any]`, one for each HTTP method. The `#[Any]` attribute matches every method that does not have a more specific route.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Raxos\Router\Attribute\{Controller, Delete, Get, Post};

#[Controller('/todos')]
final readonly class TodoController
{
    #[Get('/')]
    public function index(): array
    {
        return ['todos' => []];
    }

    #[Post('/')]
    public function store(): array
    {
        return ['created' => true];
    }

    #[Delete('/$id')]
    public function destroy(int $id): array
    {
        return ['deleted' => $id];
    }
}
```

The route attributes are repeatable, so a single method can answer more than one method or path:

```php
#[Get('/')]
#[Get('/index')]
public function index(): array
{
    return ['todos' => []];
}
```

## Path parameters

A path parameter is written as `$name` inside a route path. The router matches it against the method parameter with the same name and converts it to that parameter's type.

```php
#[Get('/$id')]
public function show(int $id): array
{
    return ['id' => $id];
}
```

Path parameters support the simple types (`string`, `int`, `float` and `bool`), backed enums, and any type implementing `StringParsableInterface` from [foundation](/foundation/). A backed enum parameter is resolved with `tryFrom`, and a `StringParsableInterface` type is resolved with `fromString`.

```php
enum TodoStatus: string
{
    case Open = 'open';
    case Done = 'done';
}

#[Get('/status/$status')]
public function byStatus(TodoStatus $status): array
{
    return ['status' => $status->value];
}
```

A parameter with a default value becomes optional in the matched path.

## Nested controllers

The `#[Child]` attribute nests one controller under another. The child inherits the parent's prefix and extends it with its own, which lets you compose a resource tree without repeating path segments.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Raxos\Router\Attribute\{Child, Controller, Get};

#[Controller('/todos')]
#[Child(TodoCommentController::class)]
final readonly class TodoController
{
    #[Get('/')]
    public function index(): array
    {
        return ['todos' => []];
    }
}

#[Controller('/$todoId/comments')]
final readonly class TodoCommentController
{
    #[Get('/')]
    public function index(int $todoId): array
    {
        return ['todo' => $todoId, 'comments' => []];
    }
}
```

Here the comment index resolves at `/todos/$todoId/comments`. The `#[Controller]` prefix of a child may itself contain a path parameter such as `$todoId`.

## Building and resolving a router

`Router::createFromControllers` maps a list of controller classes once into a `Router` instance. The optional first argument is a [container](/container/), used to resolve constructor dependencies that are not path, query or header values.

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

`resolve` matches every incoming request against the compiled static and dynamic tables and runs the matching controller method. Routes without parameters are stored as static routes and matched by a direct lookup; routes with parameters are stored as dynamic routes and matched with a combined regular expression, so resolution stays fast even with many routes.

::: tip
When you have already computed a mapping (for example from a build step or a cache), pass it to `Router::createFromMapping` to skip the reflection-based mapping step. See the [Router reference](/router/api/Router) for details.
:::
