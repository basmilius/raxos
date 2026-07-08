---
outline: deep
---

# Installation

Install Search with Composer.

```shell
composer require raxos/search
```

## Requirements

- PHP 8.5 or newer.
- The `ext-ctype` extension.
- The `ext-mbstring` extension.

## Raxos dependencies

Search builds on several other packages in the monorepo. Composer resolves these automatically, but it helps to know where the pieces come from:

- [raxos/contract](/contract/): the `FilterInterface`, `PolicyInterface`, `StructuredFilterInterface` and query node contracts.
- [raxos/database](/database/): filters operate on a query interface, and every searchable class extends the ORM `Model`.
- [raxos/error](/error/): every exception in `Raxos\Search\Error` extends the base exception class.
- [raxos/datetime](/datetime/): date and datetime range parsing is built on the date primitives.
- [raxos/foundation](/foundation/): shared utilities used across the package.
- [raxos/reflection](/reflection/): the model attributes are read with the reflection helpers.

## Configure a connection first

Search does not open its own database connection. Configure [raxos/database](/database/) with a connection before you register any model, otherwise a search cannot build or run its queries.

Return to the [Search introduction](/search/).
