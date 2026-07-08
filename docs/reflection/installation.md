---
outline: deep
---

# Installation

Install Reflection with Composer.

```shell
composer require raxos/reflection
```

## Requirements

- PHP 8.5 or newer.

The package has no required PHP extensions beyond the Reflection support built into PHP itself.

## Raxos dependencies

Reflection builds on one other Raxos package, which Composer installs for you:

- [Contract](/contract/) provides the `ReflectorInterface` that every reflector implements, and the `SerializableInterface` implemented by `MethodReflector`.

Return to the [Reflection introduction](/reflection/).
