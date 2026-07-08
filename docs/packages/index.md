---
outline: deep

cards:
    core:
        -   title: Foundation
            details: 'Base utilities used across Raxos: access traits, network and IP helpers, an Option type and small helpers.'
            link: /foundation/
        -   title: Contract
            details: 'Interfaces and contracts shared by every other Raxos module.'
            link: /contract/
        -   title: Error
            details: 'Base exception classes with a stable ExceptionId.'
            link: /error/
        -   title: Reflection
            details: 'Wrappers around PHP reflection for classes, methods, properties and types.'
            link: /reflection/
        -   title: Container
            details: 'A dependency injection container driven by PHP attributes.'
            link: /container/
        -   title: Collection
            details: 'Collection primitives such as ArrayList, Map and Paginated.'
            link: /collection/
        -   title: DateTime
            details: 'Date and time primitives and utilities.'
            link: /datetime/
        -   title: Security
            details: 'JWT, HMAC, NanoId, ULID, TOTP two factor and token helpers.'
            link: /security/
    http:
        -   title: HTTP
            details: 'Request and response objects, methods, validation and an HTTP client.'
            link: /http/
        -   title: Router
            details: 'A fast, attribute based router with controllers and middleware.'
            link: /router/
        -   title: OAuth2
            details: 'OAuth2 server integration for raxos/router.'
            link: /oauth2/
        -   title: Rate Limit
            details: 'Redis backed rate limiting for raxos/router.'
            link: /rate-limit/
        -   title: OpenAPI
            details: 'Generates an OpenAPI 3.1 specification from PHP attributes.'
            link: /openapi/
    data:
        -   title: Database
            details: 'An ORM and query builder for MySQL, MariaDB and SQLite over PDO.'
            link: /database/
        -   title: Search
            details: 'A search provider built on top of raxos/database.'
            link: /search/
        -   title: Cache
            details: 'A Redis based cache with tag support.'
            link: /cache/
    integrations:
        -   title: Mail
            details: 'Mail delivery through Mailgun, Postmark and SMTP.'
            link: /mail/
        -   title: Message Bus
            details: 'A message bus with queue support.'
            link: /message-bus/
        -   title: Barcode
            details: 'Barcode generators such as QR and PDF417 using GD.'
            link: /barcode/
        -   title: Wallet
            details: 'Apple and Google Wallet pass generator.'
            link: /wallet/
        -   title: Terminal
            details: 'A CLI framework with commands, middleware and a printer.'
            link: /terminal/
---

# Packages

Raxos is a collection of twenty-one PHP libraries. Each one solves a single concern, targets
PHP 8.5, and can be installed on its own with Composer. There is no meta package to pull in.

The libraries are grouped into four areas below. Every package links to its own introduction,
installation guide and API reference.

## Core

Foundational building blocks that the rest of the ecosystem depends on.

<LinkCards group="core"/>

## HTTP & Web

Everything you need to build an HTTP API: a typed HTTP layer, routing, authentication, rate
limiting and specification generation.

<LinkCards group="http"/>

## Data

Persistence and retrieval: an ORM, a search layer on top of it, and a cache.

<LinkCards group="data"/>

## Integrations & Output

Talking to the outside world: mail, messaging, barcodes, wallet passes and a terminal framework.

<LinkCards group="integrations"/>

## How they fit together

Most packages build on [`foundation`](/foundation/) and the interfaces in [`contract`](/contract/).
A few notable relationships:

- [`router`](/router/) builds on [`http`](/http/), and [`oauth2`](/oauth2/), [`rate-limit`](/rate-limit/) and [`openapi`](/openapi/) extend the router.
- [`search`](/search/) is built on top of [`database`](/database/).
- [`security`](/security/), [`cache`](/cache/) and [`datetime`](/datetime/) are used throughout the HTTP and data layers.

::: tip Install only what you need
Every package declares its own dependencies, so installing one pulls in just the Raxos modules it
actually uses. Start with the [Guide](/guide/) for installation and conventions.
:::
