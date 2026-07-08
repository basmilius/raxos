---
outline: deep
---

# Installation

Install Terminal with Composer.

```shell
composer require raxos/terminal
```

## Requirements

- PHP 8.5 or newer.
- The `mbstring` extension (`ext-mbstring`).

The package pulls in a few third-party libraries: [league/climate](https://climate.thephpleague.com) for terminal output and prompts, and [nunomaduro/collision](https://github.com/nunomaduro/collision) for rich error reporting.

## Raxos dependencies

Terminal builds on other Raxos packages:

- [foundation](/foundation/): the `env()` helper used by the `Caution` and `Environment` middleware, and the `Option` value type used while parsing commands.
- [contract](/contract/): the `CommandInterface`, `MiddlewareInterface` and `TerminalInterface` contracts that commands, middleware and custom terminals implement.
- [error](/error/): the base `Exception` class that every exception in `Raxos\Terminal\Error` extends.
- [collection](/collection/): the `ArrayList` used by the built-in help command to sort and filter registered commands.

The [container](/container/) package is optional. Pass a container to the `Terminal` constructor when your commands request services through untyped constructor parameters.

Return to the [Terminal introduction](/terminal/).
