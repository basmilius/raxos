---
outline: deep
---

# Installation

Install Container with Composer.

```shell
composer require raxos/container
```

## Requirements

- PHP 8.5 or newer.

The package has no required PHP extensions.

## Raxos dependencies

Container builds on a few other Raxos packages, which Composer installs for you:

- [Contract](/contract/) provides the `ContainerInterface`, `ContainerExceptionInterface` and `AttributeInterface` that the container and its attributes implement.
- [Error](/error/) provides the base `Exception` class that every container exception extends.
- [Foundation](/foundation/) provides the `env()` helper used by the `#[Env]` attribute.

Autowiring itself is built on the class, method and parameter reflectors from [Reflection](/reflection/).

## Optional dependency

To expose the container through the PSR-11 `ContainerInterface`, install `psr/container` as well. It is only needed for [`PsrContainerAdapter`](/container/api/PsrContainerAdapter).

```shell
composer require psr/container
```

Return to the [Container introduction](/container/).
