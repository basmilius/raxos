---
outline: deep
---

# Binding and resolving

The `Container` is the entry point of the package. You register how an abstract identifier should be built, then ask the container for an instance. This page covers the core registration and lookup methods; automatic resolution of unregistered classes is covered in [Autowiring and attributes](/container/autowiring).

## Creating a container

```php
<?php
declare(strict_types=1);

use Raxos\Container\Container;

$container = new Container();
```

The constructor accepts a `production` flag and two optional maps of initial definitions and singletons:

```php
$container = new Container(
    production: true,
    definitions: [],
    singletons: [],
);
```

When `production` is `true`, the container skips dependency chain tracking for performance. Outside production mode it records a [`DependencyChain`](/container/errors) for each resolution, which makes exceptions far easier to diagnose. A fresh container always resolves itself: `ContainerInterface::class`, `Container::class` and the concrete class you instantiated all return the container instance.

## Binding

`bind` registers how an abstract should be built. The concrete side can be a class string or a factory closure that receives the container:

```php
$container->bind(LoggerInterface::class, FileLogger::class);

$container->bind(LoggerInterface::class, static fn(Container $container): LoggerInterface =>
    new FileLogger($container->get(Filesystem::class)));
```

A binding registered with `bind` is rebuilt every time it is resolved. Use `bindIf` to register a binding only when one does not already exist:

```php
$container->bindIf(LoggerInterface::class, FileLogger::class);
```

## Singletons

`singleton` registers a binding that is created once and then reused for every later `get`. The concrete side is again a class string or a factory closure:

```php
$container->singleton(Database::class, static fn(Container $container): Database =>
    new Database($container->get(Config::class)));
```

`singletonIf` registers a singleton only when nothing is bound yet, and `instance` registers an object that is already constructed:

```php
$container->singletonIf(Clock::class, SystemClock::class);
$container->instance(Config::class, new Config(['debug' => false]));
```

All three accept an optional `tag` to register a named variant of the same abstract. See [tagged bindings](#tagged-bindings) below.

## Resolving

`get` returns an instance for the abstract. If a definition or singleton exists it is used; otherwise the container autowires the class by reflecting its constructor.

```php
$logger = $container->get(LoggerInterface::class);
```

`has` reports whether a definition or singleton is registered for the abstract:

```php
if ($container->has(LoggerInterface::class)) {
    // ...
}
```

## Tagged bindings

A tag scopes a binding to a named or enum backed variant of the same abstract. This lets you register several implementations of one interface and select between them explicitly. The `singleton`, `singletonIf`, `instance`, `get` and `has` methods all take a `tag` argument, which may be a string or a `UnitEnum`:

```php
$container->instance(DatabaseConnection::class, $readReplica, tag: 'read');
$container->instance(DatabaseConnection::class, $primary, tag: 'write');

$read = $container->get(DatabaseConnection::class, tag: 'read');
```

`tagged` iterates every resolved singleton that shares a tag, keyed by its abstract:

```php
foreach ($container->tagged('read') as $abstract => $instance) {
    // ...
}
```

## Removing a binding

`unbind` removes the definition and singleton for an abstract. Pass `tagged: true` to also drop every tagged variant of that abstract:

```php
$container->unbind(DatabaseConnection::class);
$container->unbind(DatabaseConnection::class, tagged: true);
```
