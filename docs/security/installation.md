---
outline: deep
---

# Installation

Install Security with Composer.

```shell
composer require raxos/security
```

## Requirements

- PHP 8.5 or newer.
- The `ext-openssl` extension, used by the RSA JWT algorithms.

## Raxos dependencies

Security builds on a few other Raxos packages, which Composer installs for you:

- [raxos/contract](/contract/): the exception interfaces such as `JwtExceptionInterface` and `UlidExceptionInterface`.
- [raxos/error](/error/): the base exception class that every Security exception extends.
- [raxos/foundation](/foundation/): the `Stopwatch` utility used by `TimingAttackPrevention`.

Return to the [Security introduction](/security/).
