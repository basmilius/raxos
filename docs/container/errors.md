---
outline: deep
---

# Errors and dependency chains

Every exception the container raises implements `ContainerExceptionInterface` from [Contract](/contract/), and extends the base `Exception` from [Error](/error/). You can therefore catch a single interface to handle any container failure:

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Container\ContainerExceptionInterface;

try {
    $service = $container->get(ReportService::class);
} catch (ContainerExceptionInterface $err) {
    // Handle any resolution failure.
}
```

## The exceptions

| Exception | Raised when |
| --- | --- |
| `DependencyCannotInstantiateException` | The target is an interface or abstract class with no binding, so it cannot be constructed. |
| `DependencyCannotAutowireException` | A built-in typed parameter or `#[Env]` value cannot be resolved and has no fallback. |
| `TaggedDependencyNotFoundException` | A tagged dependency was requested but no binding exists for that tag. |
| `CircularDependencyDetectedException` | A class depends on itself, directly or through a cycle of other classes. |
| `InvalidCallableException` | `call` received a value that is not a closure, a valid `[target, method]` pair, or an invokable class string. |
| `AutowireFailedException` | An unexpected error occurred while autowiring a parameter. |
| `ReflectionFailedException` | An underlying `ReflectionException` was thrown; it also implements `ReflectionFailedExceptionInterface`. |
| `NotFoundException` | Used by the PSR-11 adapter; implements the PSR-11 `NotFoundExceptionInterface`. |

## Circular dependencies

While resolving, the container records each class, function and method it visits. If it encounters one it is already resolving, it raises a `CircularDependencyDetectedException` instead of recursing forever. Breaking the cycle with a `#[Proxy]` parameter is often the simplest fix, since the proxied dependency is only built on first use.

## Dependency chains

Outside production mode, the container attaches a `DependencyChain` to the exceptions that describe a missing or unresolvable dependency. The chain records where resolution started (the file and line of the `get` or `call`) and the ordered path of dependencies that led to the failure:

```php
<?php
declare(strict_types=1);

use Raxos\Container\Error\DependencyCannotAutowireException;

try {
    $container->get(ReportService::class);
} catch (DependencyCannotAutowireException $err) {
    $chain = $err->chain;

    if ($chain !== null) {
        echo "Started at {$chain->fileName}:{$chain->line}\n";
        echo "Failed on {$chain->last()->name}\n";
    }
}
```

`DependencyChain` exposes `first()` and `last()` for the ends of the path, and `toArray()` for the full list of `Dependency` value objects. Each `Dependency` carries a `name`, `shortName` and `typeName`, and an `equals()` method to compare two of them. When the container runs with `production: true`, no chain is tracked and the `chain` property is `null`.

::: tip
Keep `production` set to `false` in development so failures come with a full dependency chain, and set it to `true` in production to skip the tracking overhead.
:::

## Exception properties

The table above says when each exception is raised. Each one also carries public `readonly` properties that a `catch` block can read to report the failure. For the full `DependencyChain` and `Dependency` signatures, see the [DependencyChain reference](/container/api/DependencyChain).

| Exception | Properties |
| --- | --- |
| `DependencyCannotInstantiateException` | `?DependencyChain $chain`, `?Dependency $dependency` |
| `DependencyCannotAutowireException` | `?DependencyChain $chain`, `?Dependency $dependency` |
| `TaggedDependencyNotFoundException` | `?DependencyChain $chain`, `?Dependency $dependency`, `string $tag` |
| `CircularDependencyDetectedException` | `?DependencyChain $chain`, `?Dependency $dependency` |
| `InvalidCallableException` | `mixed $callable` |
| `AutowireFailedException` | `Throwable $err` |
| `ReflectionFailedException` | `ReflectionException $err` |
| `NotFoundException` | `string $id` |

The three resolution failures and `CircularDependencyDetectedException` expose the `chain` and `dependency` that failed. `TaggedDependencyNotFoundException` adds the requested `tag`. `InvalidCallableException` hands back the offending `callable`. `AutowireFailedException` and `ReflectionFailedException` wrap the underlying throwable in `err`, and `NotFoundException` reports the missing `id`.

```php
<?php
declare(strict_types=1);

use Raxos\Container\Error\TaggedDependencyNotFoundException;

try {
    $container->get(CacheStore::class);
} catch (TaggedDependencyNotFoundException $err) {
    echo "No binding for tag {$err->tag}\n";

    if ($err->dependency !== null) {
        echo "While resolving {$err->dependency->name}\n";
    }
}
```
