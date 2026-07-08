---
outline: deep
---

# Installation

Install OpenAPI with Composer.

```shell
composer require raxos/openapi
```

## Requirements

- PHP 8.5 or newer.
- The `fileinfo`, `json` and `simplexml` extensions (`ext-fileinfo`, `ext-json`, `ext-simplexml`).

The package also pulls in `jetbrains/phpstorm-attributes` (used to read `ArrayShape` docblocks on `JsonSerializable` models) and `symfony/yaml` (used to render the document as YAML).

## Raxos dependencies

OpenAPI builds on several other Raxos packages:

- [raxos/database](/database/): ORM models and their attributes are reflected into schemas, and `#[FilterParams]` reads their filters.
- [raxos/foundation](/foundation/): reflection utilities used to resolve property types.
- [raxos/http](/http/): response codes and request models used in `#[Response]` and `#[Endpoint]`.
- [raxos/router](/router/): the router that `RouterBuilder` reflects into paths.
- [raxos/search](/search/): structured filters that `#[FilterParams]` turns into query parameters.

Return to the [OpenAPI introduction](/openapi/).
