# Testing Raxos

Each of the 21 libraries has a Pest suite, a local bootstrap, a `phpunit.xml` and a committed Composer lock file. The root configuration runs all suites against the local libraries. Fixtures and mocks use synthetic data.

Tests and fixtures omit PHPDocs. Keep ordinary comments when they explain a workaround or reference an external test vector.

Name unit tests after the source unit and mirror its directory: `router/src/Router.php` has `router/tests/RouterTest.php`, and `database/src/Orm/Backbone.php` has `database/tests/Orm/BackboneTest.php`. Keep multi-unit regression and integration tests alongside them. Assert returned values, mutations, generated payloads and failures rather than private implementation details. Shared synthetic models and SDK doubles belong under `tests/Fixtures`.

## Run the workspace

Initialize the submodules and install the development dependencies:

```sh
git submodule update --init --recursive
composer install --no-interaction --prefer-dist
php tools/validate.php
composer test:lint
composer test:types
composer test
```

Run the release-tool tests with `python3 -m unittest discover -s tests/release -v`. They use temporary Git repositories and a fake GitHub API and are also included in the root Tests workflow.

PHP 8.5 is required. Every suite fixes the default timezone to UTC, including when invoked with `composer test`. The CI extension set is `bcmath`, `ctype`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `pdo_sqlite`, `redis`, `simplexml` and `zip`. SQLite tests use an in-memory database. Mail and HTTP-provider payloads use SDK mocks and do not contact providers. Queue integration tests also use a disposable RabbitMQ 4 broker.

Redis integration tests require a disposable Redis service. Configure it explicitly:

```sh
RAXOS_REDIS_HOST=127.0.0.1 RAXOS_REDIS_PORT=6379 composer test
```

For a local Unix socket, set the host to the socket path and the port to `0`. Redis tests use random key prefixes and remove their own keys after each test. They never flush the Redis database. Without `RAXOS_REDIS_HOST`, Redis tests skip; the CI workflows always supply it.

Database and search integration tests also run on MySQL 8.4 and MariaDB 10.11. Supply DSNs for disposable databases:

```sh
RAXOS_MYSQL_DSN='mysql:host=127.0.0.1;port=3306;dbname=raxos_test' \
RAXOS_MARIADB_DSN='mysql:host=127.0.0.1;port=3307;dbname=raxos_test' \
RAXOS_MYSQL_USER=root composer test
```

Set `RAXOS_MYSQL_PASSWORD` when the test account has a password. The suites create and drop synthetic tables with `raxos_test_` and `raxos_unit_` prefixes. Use disposable databases dedicated to this suite. Each server's tests skip when its DSN is absent. The workspace, database and search workflows always provide both services.

Queue integration tests need a disposable RabbitMQ broker:

```sh
RAXOS_RABBITMQ_HOST=127.0.0.1 RAXOS_RABBITMQ_PORT=5672 \
RAXOS_RABBITMQ_USER=raxos RAXOS_RABBITMQ_PASSWORD=raxos-test composer test
```

Set the same user and password when starting the broker. The suite creates randomly named queues, including retry and dead queues, and removes its own queues. Broker tests skip when the port is absent. The root and message-bus workflows supply RabbitMQ 4 and run confirmation, delayed-retry, failed-transfer and redelivery cases.

Select a library or test by name:

```sh
vendor/bin/pest --testsuite=database
vendor/bin/pest --filter='recursive DTO'
vendor/bin/pest --log-junit=/tmp/raxos-tests.xml
```

## Run a library independently

Inside a library directory, `composer install` installs its own test runner and `composer test` runs its suite. Its bootstrap prefers its local vendor directory. Existing Composer path repositories use sibling library checkouts when present; install within the initialized workspace to test local contract changes together.

```sh
cd openapi
composer install --no-interaction --prefer-dist
composer validate --strict --no-check-all
composer test
```

Sibling requirements use bounded constraints: ^3.2 for existing capabilities and ^3.3 where new APIs are required. Composer checks the manifest schema and lock consistency. The root validation script checks that every library is registered, finds tests recursively and rejects PHPDocs in tests and fixtures.

## GitHub Actions

The root and every library have a Tests workflow triggered by pushes, pull requests and manual runs. They install PHP 8.5 and supply Redis 7. Dependencies come from the committed Composer lock files.

The workspace, database and search workflows also supply MySQL 8.4 and MariaDB 10.11. Their tests cover computed column overrides, duplicate output names, query parameters, counts, structured filters, soft deletes and model visibility during pagination.

The root workflow checks out the pinned submodule commits, validates all manifests, lints sources and tests, checks selected public API types with PHPStan, verifies bounded iteration and operation counts, and runs all 21 suites with Xdebug. It uploads JUnit test results and Clover line coverage as the `raxos-test-results` artifact, including after a failed run when files exist. Each library workflow checks out the workspace, fetches current dependency `main` branches over HTTPS, then overlays the calling library's commit and runs its local Composer install, validation and Pest suite. This allows a library change to be tested before the root updates its pointer.

The test workflows have read-only repository permissions and disable persisted checkout credentials. Workflows write test results, coverage and release plans to the runner's temporary directory before uploading them as artifacts.

## Test scope

The suites exercise successful operations, boundary values and error paths. They include individual collection/container/reflection units, all HTTP validation constraints, PSR-7/PSR-18 behavior, controller mapping and middleware, SQLite ORM lifecycle and all seven built-in relation types, and native MySQL/MariaDB expressions, writes and fulltext queries. Real Redis tests cover cache groups, invalidation and rate limiting. QR codes are decoded independently; Wallet archives are inspected and their detached CMS signatures verified using an ephemeral local certificate. Security tests include all RFC 6238 SHA-1/SHA-256/SHA-512 vectors, RFC 7636 PKCE and JWT algorithm separation.

AMQP connection/channel unit tests and mail-provider payloads use SDK mocks. Native RabbitMQ tests cover confirmed routing, retry limits, delayed retries, dead queues and redelivery after a failed transfer. Actual mail deliveries and Apple device acceptance require provider environments. Composite Every/Some filters execute truth and weighted-score checks on SQLite, MySQL and MariaDB. OAuth rotation tests run two PHP processes against transactional SQLite storage; production token adapters remain application-owned. The contract suite verifies that every declared interface and enum can be loaded independently; implementations are tested in their owning libraries.

Line coverage is measured with Xdebug across every library's `src` directory, including untouched code. It is not branch coverage or a guarantee that all inputs work. File-download tests run in separate PHP processes, so their executed lines are not collected by the parent coverage driver. No minimum line-coverage threshold is enforced.

To collect the same artifacts locally, install Xdebug and configure Redis, MySQL, MariaDB and RabbitMQ, then run:

```sh
raxos_results=$(mktemp -d)
XDEBUG_MODE=coverage php -d date.timezone=UTC -d memory_limit=2G vendor/bin/pest \
  --log-junit="$raxos_results/junit.xml" --coverage-clover="$raxos_results/clover.xml"
```

The regression suites cover model visibility, duplicate projection names, soft-delete scopes and filtered pagination on SQLite, MySQL and MariaDB. [MIGRATION.md](MIGRATION.md) lists the contract and behavior changes required before upgrading.

## Reproduce the performance measurements

```sh
RAXOS_REDIS_HOST=127.0.0.1 RAXOS_REDIS_PORT=6379 php tools/benchmark.php > /tmp/raxos-performance.json
```

The script measures lexer growth, suffix-list reloads, route registration, isolated JSON parsing, batched SQLite hydration, retained identities, local file sending and tagged Redis invalidation. CPU timings are medians after warmup. The Redis flush is a single run. File timing excludes process startup and network transfer. Results are machine dependent; query counts, batch sizes and retained identities give more stable regression signals.

## Push order

Each library is a separate Git repository. Push library commits before the root commit so the root's submodule pointers resolve. To avoid dependent package CI running against old contracts, push in these groups, finishing one group before starting the next:

1. `contract`
2. `error`, `reflection`
3. `foundation`
4. `collection`, `security`, `mail`
5. `barcode`, `cache`, `container`, `database`, `terminal`
6. `datetime`, `message-bus`
7. `http`, `search`
8. `router`
9. `oauth2`, `openapi`, `rate-limit`, `wallet`
10. Root workspace

Run `git push origin main` from each listed repository. The current changes prepare local commits only; they do not create version tags or publish a release.

## Public types and bounded iteration

`composer test:types` runs PHPStan level 5 on collection/container contracts, collection implementations, the HTTP client and selected new policy objects. `tests/Types/PublicApi.php` verifies inferred collection values, nullability, fallback values, class-string resolution and public request builders. This is an explicit scope, with no baseline or ignored error list; it does not claim static coverage of every existing source file.

```sh
php tools/benchmark-features.php --check > /tmp/raxos-feature-measurements.json
php tools/benchmark-runtime.php > /tmp/raxos-runtime-measurements.json
```

The script compares eager lists, LazySequence and ORM keyset iteration in fresh PHP processes. It gates row checksums, a generous bounded PHP-heap budget, expected query counts, zero retained ORM identities and one Redis evaluation per native snapshot. It does not gate machine-dependent timing. Heap measurements exclude database-driver allocations and RSS. Query counts run in a separate pass so retained logger events do not distort memory results.

The runtime script measures reflection metadata, constructor autowiring, registration and resolution of 400 routes, QR SVG/PNG rendering and signing a Wallet pass with 16 disk attachments. It separates first-use time from warmed batch medians. Wallet certificate generation is excluded; the local signature does not establish Apple device acceptance. `RAXOS_BENCH_BASELINE_DIR` can point to exported library sources for a comparison using the same third-party dependencies. Current autoloaded function files remain active in that comparison.
