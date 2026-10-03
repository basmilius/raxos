<a href="https://bas.dev">
    <img src="https://bmcdn.nl/assets/branding/logo.svg" alt="Bas Milius" height="48" />
</a>

---

# Raxos

Twenty-one PHP libraries for HTTP applications, persistence, background work and shared utilities. Each library has its own Composer package, repository and Pest suite. All require PHP 8.5 or later.

The root repository contains the documentation, shared test tooling and pinned library checkouts as Git submodules.

[Documentation](https://raxos.dev) | [Package guide](https://raxos.dev/packages/) | [Release 3.2.0](https://github.com/basmilius/raxos/releases/tag/3.2.0)

## Installation

Install the libraries your application uses:

```sh
composer require "raxos/router:^3.2"
```

Composer resolves the required sibling packages and checks their PHP extension requirements. Each library's README covers its installation and a first example. Upgrade related Raxos packages together; [Migrating to 3.2.0](MIGRATION.md) explains the changed contracts and behavior.

## Libraries

| Package | Purpose |
| --- | --- |
| [raxos/barcode](barcode/README.md) | QR and PDF417 encoding with PNG and SVG output. |
| [raxos/cache](cache/README.md) | Redis commands, cached computations and tagged invalidation. |
| [raxos/collection](collection/README.md) | Mutable and read-only lists, maps and paginated results. |
| [raxos/container](container/README.md) | Constructor injection, bindings, singletons and tagged services. |
| [raxos/contract](contract/README.md) | Shared interfaces and implementation extension points. |
| [raxos/database](database/README.md) | PDO queries and ORM models for SQLite, MySQL and MariaDB. |
| [raxos/datetime](datetime/README.md) | Immutable date, time and timestamp values built on Chronos. |
| [raxos/error](error/README.md) | Structured exceptions and repeatable numeric exception IDs. |
| [raxos/foundation](foundation/README.md) | Options, access traits, IP parsing and shared utilities. |
| [raxos/http](http/README.md) | Requests, responses, clients, validation and file streaming. |
| [raxos/mail](mail/README.md) | Typed messages and SMTP, Mailgun and Postmark adapters. |
| [raxos/message-bus](message-bus/README.md) | RabbitMQ queues, priorities and registered message classes. |
| [raxos/oauth2](oauth2/README.md) | OAuth2 server controllers, factory contracts and bearer middleware. |
| [raxos/openapi](openapi/README.md) | OpenAPI 3.1.1 documents generated from controllers and PHP types. |
| [raxos/rate-limit](rate-limit/README.md) | Redis operation quotas and router middleware. |
| [raxos/reflection](reflection/README.md) | Typed class, member, parameter, function and type reflectors. |
| [raxos/router](router/README.md) | Attribute-based HTTP controllers, mapping and middleware. |
| [raxos/search](search/README.md) | ORM search, query syntax, structured filters and policies. |
| [raxos/security](security/README.md) | JWT, TOTP, identifiers, HMAC and token utilities. |
| [raxos/terminal](terminal/README.md) | CLI commands, typed arguments, options, help and output. |
| [raxos/wallet](wallet/README.md) | Apple Wallet pass models, signing and archive generation. |

The router uses the container and HTTP layer. OAuth2, rate limiting and OpenAPI add server features around that router. Search builds on the database ORM. Other dependencies are declared in each package's `composer.json`.

## A first route

```php
<?php
declare(strict_types=1);

use Raxos\Container\Container;
use Raxos\Http\HttpRequest;
use Raxos\Http\Response\JsonHttpResponse;
use Raxos\Router\Attribute\Controller;
use Raxos\Router\Attribute\Get;
use Raxos\Router\Router;

require __DIR__ . '/vendor/autoload.php';

#[Controller('/health')]
final readonly class HealthController
{
    #[Get('/')]
    public function index(): JsonHttpResponse
    {
        return new JsonHttpResponse(['status' => 'ok']);
    }
}

$router = Router::createFromControllers(new Container(), [HealthController::class]);
$router->resolve(HttpRequest::createFromGlobals())->send();
```

Put this entry point behind your web server and request `/health`. Route parameters use `$name`, such as `#[Get('/products/$id')]` for a method accepting `int $id`.

## Development and testing

Clone the workspace with its submodules and install the development dependencies:

```sh
git clone --recurse-submodules https://github.com/basmilius/raxos.git
cd raxos
composer install
php tools/validate.php
composer test:lint
composer test
```

Run one library with `vendor/bin/pest --testsuite=router`. Redis integration tests need a disposable Redis service. Database and search tests also use MySQL and MariaDB, alongside SQLite. [Testing Raxos](TESTING.md) documents the extensions, service configuration, test scope and coverage collection.

GitHub Actions runs the workspace against its pinned commits and each library against its current dependencies. The root [Tests workflow](.github/workflows/tests.yml) validates manifests, lints PHP, tests release tooling and uploads JUnit and coverage artifacts.

Each library is a separate Git repository. Commit library changes in their repository, then update the corresponding root submodule pointer. [TESTING.md](TESTING.md#push-order) lists the dependency order for pushes.

## Releases

One GitHub release in the root repository publishes the same version in all 21 libraries, using the exact commits pinned by the root release. Each library receives its own tag and reviewed release notes. Notes are stored in GitHub release bodies and the root release asset.

Follow [Releasing Raxos](RELEASING.md) for setup, agent preparation, approval and recovery. The workflow checks the release data and successful root CI before publishing.

## License

[MIT](LICENSE). Copyright (c) 2017 - present Bas Milius.
