---
outline: deep
---

# Introduction

Welcome to **Raxos**, a collection of twenty-one PHP libraries that power my personal projects.
Every library is published independently on [Packagist](https://packagist.org/packages/raxos/),
targets **PHP 8.5**, and follows the same engineering conventions: strict types, final and readonly
classes where possible, attribute driven configuration, and documented public APIs.

## What's inside

The packages are grouped into four areas.

- **Core** provides the foundation the rest builds on: [`foundation`](/foundation/),
  [`contract`](/contract/), [`error`](/error/), [`reflection`](/reflection/),
  [`container`](/container/), [`collection`](/collection/), [`datetime`](/datetime/) and
  [`security`](/security/).
- **HTTP & Web** covers the request lifecycle: [`http`](/http/), [`router`](/router/),
  [`oauth2`](/oauth2/), [`rate-limit`](/rate-limit/) and [`openapi`](/openapi/).
- **Data** handles persistence: [`database`](/database/), [`search`](/search/) and
  [`cache`](/cache/).
- **Integrations & Output** reaches the outside world: [`mail`](/mail/),
  [`message-bus`](/message-bus/), [`barcode`](/barcode/), [`wallet`](/wallet/) and
  [`terminal`](/terminal/).

## Where to start

- New here? Read [Getting started](/guide/getting-started) for installation and a first taste.
- Want the big picture? The [Packages overview](/packages/) shows all twenty-one packages and how
  they relate.
- Curious about the code style? See [Conventions](/guide/conventions) for the patterns every
  package follows.
- Wondering how the repository is laid out? See [Monorepo](/guide/monorepo).
