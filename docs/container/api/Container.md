---
outline: deep
---

# Container

`Raxos\Container\Container`

The default dependency injection container. It implements `Raxos\Contract\Container\ContainerInterface` and autowires classes, functions and methods through reflection.

```php
class Container implements ContainerInterface
```

## Constructor

```php
public function __construct(
    public readonly bool $production = false,
    array $definitions = [],
    array $singletons = [],
)
```

Creates a container. In production mode, dependency chains are not tracked, which speeds up resolution at the cost of less detailed exceptions. `$definitions` and `$singletons` seed the container with initial bindings. The new container always resolves itself for `ContainerInterface::class`, `Container::class` and its concrete class.

## Binding methods

### bind

```php
public function bind(string $abstract, callable|string|null $concrete): void
```

Registers a class string or factory closure for an abstract identifier. The binding is rebuilt on every resolution.

### bindIf

```php
public function bindIf(string $abstract, callable|string|null $concrete): void
```

Registers a binding only if one does not already exist.

### singleton

```php
public function singleton(string $abstract, callable|string|null $concrete, UnitEnum|string|null $tag = null): void
```

Registers a singleton binding, optionally scoped by a tag. The concrete value is built once and reused for every later resolution.

### singletonIf

```php
public function singletonIf(string $abstract, callable|string|null $concrete, UnitEnum|string|null $tag = null): void
```

Registers a singleton binding only if one does not already exist.

### instance

```php
public function instance(string $abstract, object $instance, UnitEnum|string|null $tag = null): void
```

Registers an already constructed instance as a singleton, optionally scoped by a tag.

### unbind

```php
public function unbind(string $abstract, bool $tagged = false): void
```

Removes the definition and singleton for an abstract. When `$tagged` is `true`, every tagged variant of the abstract is removed as well.

## Lookup methods

### get

```php
public function get(string $abstract, UnitEnum|string|null $tag = null): object
```

Resolves and returns an instance for the abstract, autowiring it if no binding exists. Optionally scoped by a tag.

### has

```php
public function has(string $abstract, UnitEnum|string|null $tag = null): bool
```

Checks whether a definition or singleton exists for the abstract and tag.

### tagged

```php
public function tagged(UnitEnum|string $tag): iterable
```

Yields all resolved singletons sharing the given tag, keyed by their abstract.

## Invocation

### call

```php
public function call(Closure|array|string $callable, array $args = []): mixed
```

Invokes a closure, an `[target, method]` pair, or an invokable class string, autowiring every parameter not present in `$args`. Named arguments in `$args` override autowired values.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Container\Container;

$container = new Container();

$container->singleton(Database::class, static fn(Container $container): Database =>
    new Database($container->get(Config::class)));

$container->instance(DatabaseConnection::class, $readReplica, tag: 'read');

$database = $container->get(Database::class);
$reader = $container->get(DatabaseConnection::class, tag: 'read');

$count = $container->call([$reportController, 'index'], [
    'page' => 1,
]);
```
