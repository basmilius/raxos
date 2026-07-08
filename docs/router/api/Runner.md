---
outline: deep
---

# Runner

`Raxos\Router\Runner` executes the frame stack for a matched route. It builds the middleware and controller pipeline for a single request and runs it. The router creates a `Runner` internally for each resolved request; you rarely construct one yourself.

```php
final class Runner
{
    public function __construct(
        public readonly RouterInterface $router,
        public readonly FrameStack $stack
    );
}
```

## Methods

### run

```php
public function run(HttpRequest $request): HttpResponse
```

Runs the frame stack for the request and returns the resulting response. It composes the frames into a nested pipeline, sets the current request and frame as router globals, and executes them in order. An exception that is not already a router runtime exception is wrapped in an `UnexpectedException` that carries the failing frame's description.

### singleton

```php
public function singleton(string $controller, ?callable $setup = null): mixed
```

Returns the controller instance for this run, creating it lazily (once) through the `$setup` callback. Subsequent calls for the same controller return the cached instance. Calling it without a setup for a controller that was never created throws a `ControllerNotInstantiatedException`.

## Example

The runner is used internally by the router. A middleware or frame receives it as its first argument and can reach the controller instance and the router through it:

```php
$controller = $runner->singleton(TodoController::class);
$router = $runner->router;
```
