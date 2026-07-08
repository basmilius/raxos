---
outline: deep
---

# Getting started

This guide walks you through installing one or more `raxos/*` packages into a PHP project.

## Prerequisites

- **PHP 8.5** or newer.
- [Composer](https://getcomposer.org/) 2.
- The extensions a package needs. Most of Core relies on common extensions such as `ext-json`,
  `ext-mbstring` and `ext-intl`. Each package lists its own requirements on its installation page.

## Install your first package

Every package is published on [Packagist](https://packagist.org/packages/raxos/) under the
`raxos/` vendor. Add the one you need with Composer.

::: code-group

```shell [Composer]
composer require raxos/http
```

```json [composer.json]
{
    "require": {
        "raxos/http": "^2.3"
    }
}
```

:::

Then autoload as usual and start using the classes.

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpResponse;

require __DIR__ . '/vendor/autoload.php';

HttpResponse::create()
    ->withJson(['hello' => 'world'])
    ->send();
```

## A typical web stack

For an HTTP API you usually combine a few packages: the HTTP layer, the router, and often the
database.

```shell
composer require raxos/http raxos/router raxos/database
```

From there, add [`oauth2`](/oauth2/) for authentication, [`rate-limit`](/rate-limit/) to protect
endpoints, and [`openapi`](/openapi/) to generate a specification from your controllers.

## Stability

The packages set `minimum-stability` to `dev` with `prefer-stable` enabled, so tagged releases are
preferred. If you depend on unreleased changes, mirror that setting in your own `composer.json`.

## Next steps

- Browse the full list on the [Packages overview](/packages/).
- Read the [Conventions](/guide/conventions) to understand the shared code style.
- Jump straight into a package from the **Packages** menu.
