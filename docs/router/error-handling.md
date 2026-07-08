---
outline: deep
---

# Error handling

Every exception the router raises extends `Raxos\Error\Exception` and implements `RouterExceptionInterface` from [contract](/contract/), which in turn extends the base `ExceptionInterface`. That common root lets you catch any router failure with a single type. Below the root, the hierarchy splits into two branches that answer a practical question: did the error happen while the router was being built, or while it was serving a request.

## Two kinds of failure

`RouterExceptionInterface` has two child interfaces, both in the `Raxos\Contract\Router` namespace:

- `MappingExceptionInterface` marks errors thrown while building the router, for example from `Router::createFromControllers` or `DynamicRouter::route`, when a controller or handler definition is itself invalid. These are programming mistakes and should surface during development, not in production traffic.
- `RuntimeExceptionInterface` marks errors thrown while resolving or running an actual request. It also exposes a `previous` property carrying the original throwable when one is relevant.

```php
namespace Raxos\Contract\Router;

use Throwable;

interface RuntimeExceptionInterface extends RouterExceptionInterface
{
    public ?Throwable $previous {
        get;
    }
}
```

Because both interfaces extend `RouterExceptionInterface`, you can still catch everything at once when that is what you want.

## Mapping exceptions

These are thrown once, while mapping controllers or registering dynamic routes. They point at the definition that could not be turned into a route.

| Exception | Raised when |
| --- | --- |
| `MissingTypeException` | A parameter or injected property has no type declaration. |
| `InvalidPathParameterException` | No regex could be determined for a path parameter. |
| `TypeTooComplexException` | A path parameter type is not a simple type, a backed enum, or a `StringParsableInterface`. |
| `InvalidReturnTypeException` | A route method does not declare a usable return type. |
| `MappingReflectionErrorException` | Reflection failed while mapping a controller or handler. |

All five implement `MappingExceptionInterface`. `MappingReflectionErrorException` additionally implements `ReflectionFailedExceptionInterface` and keeps the underlying `ReflectionException` on its `err` property.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Router\MappingExceptionInterface;
use Raxos\Router\Router;

try {
    $router = Router::createFromControllers($container, [
        TodoController::class,
    ]);
} catch (MappingExceptionInterface $err) {
    // A controller definition is invalid. Fix the code; do not
    // swallow this in production.
    throw $err;
}
```

## Runtime exceptions

These are thrown while a request is being resolved and run. They all implement `RuntimeExceptionInterface`.

| Exception | Raised when |
| --- | --- |
| `MissingInjectionException` | A parameter or property could not be filled from any source. |
| `InvalidInjectionException` | A value was found for a parameter or property but had the wrong type. |
| `ValidationFailedException` | A `#[Validated]` or `#[ValidatedQuery]` attribute rejected the request. |
| `ControllerNotInstantiatedException` | `Runner::singleton` was called without a setup callback before first use. |
| `EmptyResultResponseException` | A result-response was used directly instead of being converted. |
| `InvalidHandlerException` | `Router::path` or `DynamicRouter::path` was given a handler pair that is not a mapped route. |
| `MissingFileException` | The file response helper could not find its path. |
| `MissingInstanceException` | A named instance was missing. |
| `ReflectionErrorException` | Reflection failed while resolving a request. |
| `UnexpectedException` | Any other throwable raised inside a frame; it is wrapped and tagged with the failing frame. |

`ReflectionErrorException` also implements `ReflectionFailedExceptionInterface`. `UnexpectedException` keeps the original throwable on its `err` property and the failing frame on its `call` property, so an unexpected crash inside a controller still tells you where it happened.

## Catching runtime errors at the boundary

Because every request time failure implements `RuntimeExceptionInterface`, a single catch around `resolve` turns any of them into a consistent error response. This pairs naturally with the `error` helper on the `Responds` trait, documented on the [responses](/router/responses) page.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Router\RuntimeExceptionInterface;
use Raxos\Http\HttpRequest;

try {
    $response = $router->resolve(HttpRequest::create());
} catch (RuntimeExceptionInterface $err) {
    // Turn any router runtime failure into one error response.
    $response = $errorRenderer->render($err);
}

$response->send();
```

`ValidationFailedException` is the runtime exception you will meet most often. The [middleware and validation](/router/middleware#validating-the-request-body) page shows a validation example end to end, so it is not repeated here.

::: tip
Catch `MappingExceptionInterface` at startup and `RuntimeExceptionInterface` at the request boundary. Reach for the shared `RouterExceptionInterface` root only when you genuinely want to treat both the same way.
:::
