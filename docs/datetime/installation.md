---
outline: deep
---

# Installation

Install DateTime with Composer.

```shell
composer require raxos/datetime
```

## Requirements

- PHP 8.5 or newer.
- [cakephp/chronos](https://github.com/cakephp/chronos), the immutable date and time library the
  value objects extend.
- [jetbrains/phpstorm-attributes](https://github.com/JetBrains/phpstorm-attributes) for editor
  metadata.

The package has no required PHP extensions of its own.

## Raxos dependencies

- [raxos/foundation](/foundation/): provides the `StringParsableInterface` and other contracts used
  by the value objects.

## Optional integrations

- [raxos/database](/database/): enables the `DateCaster`, `TimeCaster` and `DateTimeCaster` ORM
  casters. Install it when you want to bind these value objects to model columns.
- [raxos/router](/router/): recognizes the value objects as path parameters through
  `StringParsableInterface`, so a route segment can bind directly to a `Date`, `Time` or
  `DateTime` instance.

Return to the [DateTime introduction](/datetime/).
