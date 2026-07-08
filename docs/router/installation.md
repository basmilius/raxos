---
outline: deep
---

# Installation

Install Router with Composer.

```shell
composer require raxos/router
```

## Requirements

- PHP 8.5 or newer.
- The `ctype` extension.
- The `fileinfo` extension.
- The `json` extension.
- The `simplexml` extension.

## Raxos dependencies

Router builds on a small set of other Raxos packages, which Composer installs automatically:

- [container](/container/): resolves controller and middleware dependencies that are not path, query or header values.
- [foundation](/foundation/): provides the base utilities and the `StringParsableInterface` used to type path parameters.
- [http](/http/): provides the `HttpRequest` and `HttpResponse` objects the router consumes and returns.

The [database](/database/) package is an optional dependency. It is only needed to use the `#[MapModel]` and `#[MapModelRelation]` attributes, which resolve ORM models directly from path parameters.

Return to the [Router introduction](/router/).
