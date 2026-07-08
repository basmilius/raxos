---
outline: deep
---

# Installation

Install Contract with Composer.

```shell
composer require raxos/contract
```

## Requirements

- PHP 8.5 or newer.
- The `ext-pdo` extension, because several database contracts reference PDO directly.

## Raxos dependencies

Contract sits at the bottom of the Raxos dependency graph and requires no other `raxos/*` package. In practice you rarely require it directly: it comes in transitively as a dependency of the other Raxos packages you install.

Return to the [Contract introduction](/contract/).
