---
outline: deep
---

# Monorepo

Raxos is developed as a single repository that tracks each library as a git **submodule**. Every
submodule is its own repository on GitHub under `github.com/basmilius/raxos-<name>` and is published
to Packagist as `raxos/<name>`.

## Layout

The root repository contains one directory per package, plus this documentation site under `docs/`.

```
raxos/
├── barcode/        Raxos\Barcode
├── cache/          Raxos\Cache
├── collection/     Raxos\Collection
├── container/      Raxos\Container
├── contract/       Raxos\Contract
├── database/       Raxos\Database
├── datetime/       Raxos\DateTime
├── error/          Raxos\Error
├── foundation/     Raxos\Foundation
├── http/           Raxos\Http
├── mail/           Raxos\Mail
├── message-bus/    Raxos\MessageBus
├── oauth2/         Raxos\OAuth2
├── openapi/        Raxos\OpenAPI
├── rate-limit/     Raxos\RateLimit
├── reflection/     Raxos\Reflection
├── router/         Raxos\Router
├── search/         Raxos\Search
├── security/       Raxos\Security
├── terminal/       Raxos\Terminal
├── wallet/         Raxos\Wallet
└── docs/           This documentation site
```

## Working with a submodule

Each directory is a standalone git repository. Commit and push changes from within that directory.

```shell
cd http
git add src/...
git commit -m "feat: ..."
```

The root repository tracks the submodule pointers through `.gitmodules`. After changing a submodule,
update its pointer in the root.

```shell
git add http
git commit -m "chore: bump http"
```

## Cross package dependencies

Within the monorepo, packages reference each other through Composer path repositories with symlinks,
so local changes are picked up immediately. Published releases depend on the tagged versions on
Packagist instead.
