---
outline: deep
---

# ContainerInterface

`Raxos\Contract\Container\ContainerInterface` is the dependency injection container contract implemented by [raxos/container](/container/)'s `Container` class. Application code can type hint against this interface instead of the concrete container, which keeps modules testable and swappable.

## Signature

```php
interface ContainerInterface
{
    public function bind(string $abstract, callable|string|null $concrete): void;
    public function bindIf(string $abstract, callable|string|null $concrete): void;
    public function singleton(string $abstract, callable|string|null $concrete, UnitEnum|string|null $tag = null): void;
    public function singletonIf(string $abstract, callable|string|null $concrete, UnitEnum|string|null $tag = null): void;
    public function instance(string $abstract, object $instance, UnitEnum|string|null $tag = null): void;
    public function get(string $abstract, UnitEnum|string|null $tag = null): object;
    public function has(string $abstract, UnitEnum|string|null $tag = null): bool;
    public function call(Closure|array|string $callable, array $args = []): mixed;
    public function tagged(UnitEnum|string $tag): iterable;
    public function unbind(string $abstract, bool $tagged = false): void;
}
```

## Methods

### `bind(string $abstract, callable|string|null $concrete): void`

Registers a binding for an abstract type.

### `bindIf(string $abstract, callable|string|null $concrete): void`

Registers a binding only if one does not already exist.

### `singleton(string $abstract, callable|string|null $concrete, UnitEnum|string|null $tag = null): void`

Registers a binding that is resolved only once and reused. An optional `$tag` groups related singletons.

### `singletonIf(string $abstract, callable|string|null $concrete, UnitEnum|string|null $tag = null): void`

Registers a singleton only if one does not already exist.

### `instance(string $abstract, object $instance, UnitEnum|string|null $tag = null): void`

Registers an already constructed instance as a singleton.

### `get(string $abstract, UnitEnum|string|null $tag = null): object`

Resolves and returns an instance for the given abstract. Throws a `ContainerExceptionInterface` when resolution fails.

### `has(string $abstract, UnitEnum|string|null $tag = null): bool`

Checks whether a binding exists for the given abstract.

### `call(Closure|array|string $callable, array $args = []): mixed`

Invokes a callable, autowiring any parameters it did not receive explicitly. Values in `$args` are matched by parameter name and override autowired values.

### `tagged(UnitEnum|string $tag): iterable`

Returns all resolved singletons that share the given tag, keyed by their abstract. Useful for retrieving a group of related services.

### `unbind(string $abstract, bool $tagged = false): void`

Removes a binding from the container.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Container\ContainerInterface;

final readonly class MailDispatcher
{
    public function __construct(
        private ContainerInterface $container,
    ) {}

    public function dispatch(string $transport): object
    {
        return $this->container->get($transport);
    }
}
```
