---
outline: deep
---

# Installation

Install Collection with Composer.

```shell
composer require raxos/collection
```

## Requirements

- PHP 8.5 or newer.

The package uses [jetbrains/phpstorm-attributes](https://github.com/JetBrains/phpstorm-attributes) for editor hints such as `#[ArrayShape]`. It requires no PHP extensions of its own.

## Raxos dependencies

Collection builds on three other Raxos packages, which Composer installs automatically:

- [contract](/contract/): the collection interfaces (`ArrayListInterface`, `MapInterface`, `ValidatedArrayListInterface`) and the collection exception interface that the classes implement.
- [error](/error/): the base `Exception` class that `CollectionImmutableException` and `CollectionInvalidTypeException` extend.
- [foundation](/foundation/): the `ArrayUtil` helpers used for `first`, `last`, `only` and `merge` behavior.

Return to the [Collection introduction](/collection/).
