---
outline: deep
---

# Installation

Install HTTP with Composer.

```shell
composer require raxos/http
```

## Requirements

- PHP 8.5 or newer.
- The following PHP extensions:
    - `ext-ctype`
    - `ext-fileinfo`
    - `ext-mbstring`

## Raxos dependencies

HTTP builds on a handful of other Raxos packages:

- [contract](/contract/): the interfaces that `HttpRequest`, the responses, the client and the validator implement.
- [datetime](/datetime/): the `Date`, `DateTime` and `Time` validation constraints return raxos/datetime value objects.
- [error](/error/): all client and validation exceptions extend the base `Raxos\Error\Exception` class.
- [foundation](/foundation/): `HttpRequest::ip()` returns a raxos/foundation `IP` value object and the collection maps back the request.

The package also pulls in `guzzlehttp/guzzle` for the outgoing client, plus the `psr/http-client`, `psr/http-factory` and `psr/http-message` interfaces for the PSR bridge.

::: info raxos/database
The `Model` and `ModelArray` validation constraints resolve request values into [database](/database/) ORM models. That package is only required when you use those two constraints, so it is a development and suggested dependency rather than a hard requirement.
:::

Return to the [HTTP introduction](/http/).
