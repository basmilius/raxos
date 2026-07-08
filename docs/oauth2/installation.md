---
outline: deep
---

# Installation

Install OAuth2 with Composer.

```shell
composer require raxos/oauth2
```

## Requirements

- PHP 8.5 or newer.

The package relies only on the default PHP runtime; no additional extensions are required beyond those pulled in by its Raxos dependencies.

## Raxos dependencies

OAuth2 builds on a few other Raxos packages, which Composer installs automatically:

- [cache](/cache/): a typical `TokenFactoryInterface` implementation stores access tokens, refresh tokens and authorization codes here.
- [foundation](/foundation/): shared utilities used across the Raxos ecosystem.
- [http](/http/): provides the `HttpRequest`, `HttpResponse` and `HttpResponseCode` objects every endpoint consumes and returns.
- [router](/router/): provides the controller attributes, the `Responds` trait and the middleware pipeline the server plugs into.

Return to the [OAuth2 introduction](/oauth2/).
