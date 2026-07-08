---
outline: deep
---

# Installation

Install Error with Composer.

```shell
composer require raxos/error
```

## Requirements

- PHP 8.5 or newer.
- No additional PHP extensions are required.

## Raxos dependencies

The `composer.json` of this package declares no required Raxos packages, so it can be installed on
its own. At the source level `Exception` implements the `ExceptionInterface` contract from
[raxos/contract](/contract/), which is available transitively in any project that already uses the
wider Raxos stack.

Return to the [Error introduction](/error/).
