---
outline: deep

cards:
    highlights:
        -   title: Container
            code: true
            details: 'Binds, resolves and autowires classes, functions and methods through reflection.'
            link: /container/api/Container
        -   title: PsrContainerAdapter
            code: true
            details: 'Exposes a Raxos container as a standard PSR-11 ContainerInterface.'
            link: /container/api/PsrContainerAdapter
        -   title: Singleton
            code: true
            details: 'Marks a class so its first autowired instance is cached and reused.'
            link: /container/api/Attributes
        -   title: Tag
            code: true
            details: 'Scopes a binding or a parameter to a named or enum backed variant.'
            link: /container/api/Attributes
        -   title: Inject
            code: true
            details: 'Injects a property after construction, optionally behind a lazy proxy.'
            link: /container/api/Attributes
        -   title: Env
            code: true
            details: 'Resolves a scalar parameter from an environment variable with type coercion.'
            link: /container/api/Attributes
---

# Container

The Container package provides a lightweight dependency injection container for Raxos applications. It resolves classes automatically by reflecting on constructors and method parameters, and supports explicit bindings, singletons, tagged variants, property injection and lazy proxies through PHP attributes. The same container can invoke arbitrary callables while autowiring their missing arguments, and a PSR-11 adapter lets it be handed to libraries that expect the standard `ContainerInterface`.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Binding and resolving](/container/binding-and-resolving): create a container and register classes, factories, singletons and tagged bindings, then fetch them with `get` and `has`.
- [Autowiring and attributes](/container/autowiring): how constructor and property dependencies are resolved automatically, and the `#[Inject]`, `#[Proxy]`, `#[Tag]`, `#[Singleton]` and `#[Env]` attributes that steer that behavior.
- [Calling callables](/container/calling-callables): invoke closures, static or instance methods and invokable classes while the container fills in missing arguments.
- [Errors and dependency chains](/container/errors): the exception hierarchy raised during resolution, and how `DependencyChain` helps diagnose failures.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Container\Container;

$container = new Container();
$container->singleton(LoggerInterface::class, FileLogger::class);

$logger = $container->get(LoggerInterface::class);
```

## Installation

Install the package with Composer and check the requirements on the [installation](/container/installation) page.

```shell
composer require raxos/container
```
