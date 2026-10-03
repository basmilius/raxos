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
composer test
```

Run the release-tool tests with `python3 -m unittest discover -s tests/release -v`. They use temporary Git repositories and a fake GitHub API and are also included in the root Tests workflow.

PHP 8.5 is required. Every suite fixes the default timezone to UTC, including when invoked with `composer test`. The CI extension set is `bcmath`, `ctype`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `pdo_sqlite`, `redis`, `simplexml` and `zip`. SQLite tests use an in-memory database. Mail, HTTP transport and AMQP behavior use SDK mocks and do not contact providers.

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

The validation flag preserves the project's deliberate `*` requirements for sibling Raxos libraries. Composer still checks the manifest schema and lock consistency. The root validation script checks that every library is registered, finds tests recursively and rejects PHPDocs in tests and fixtures.

## GitHub Actions

The root and every library have a Tests workflow triggered by pushes, pull requests and manual runs. They install PHP 8.5 and supply Redis 7. Dependencies come from the committed Composer lock files.

The workspace, database and search workflows also supply MySQL 8.4 and MariaDB 10.11. Their tests cover computed column overrides, duplicate output names, query parameters, counts, structured filters, soft deletes and model visibility during pagination.

The root workflow checks out the pinned submodule commits, validates all manifests, lints sources and tests, and runs all 21 suites with Xdebug. It uploads JUnit test results and Clover line coverage as the `raxos-test-results` artifact, including after a failed run when files exist. Each library workflow checks out the workspace, fetches current dependency `main` branches over HTTPS, then overlays the calling library's commit and runs its local Composer install, validation and Pest suite. This allows a library change to be tested before the root updates its pointer.

The workflows have read-only repository permissions and disable persisted checkout credentials. YAML was checked locally with actionlint. GitHub will execute these new workflows after the commits are pushed; local validation is recorded in the reports.

## Test scope

The suites exercise successful operations, boundary values and error paths. They include individual collection/container/reflection units, all HTTP validation constraints, PSR-7/PSR-18 behavior, controller mapping and middleware, SQLite ORM lifecycle and all seven built-in relation types, and native MySQL/MariaDB expressions, writes and fulltext queries. Real Redis tests cover cache groups, invalidation and rate limiting. QR codes are decoded independently; Wallet archives are inspected and their detached CMS signatures verified using an ephemeral local certificate. Security tests include all RFC 6238 SHA-1/SHA-256/SHA-512 vectors, RFC 7636 PKCE and JWT algorithm separation.

AMQP connection/channel behavior and mail-provider payloads use SDK mocks. A live RabbitMQ broker, provider deliveries, Apple's certificate trust and acceptance on a device require separate integration environments. `Search\Filter\Every` remains unsupported; its tests assert the existing exception without query mutation. The contract suite verifies that every declared interface and enum can be loaded independently; implementations are tested in their owning libraries.

Line coverage is measured with Xdebug across every library's `src` directory, including untouched code. It is not branch coverage or a guarantee that all inputs work. File-download tests run in separate PHP processes, so their executed lines are not collected by the parent coverage driver. The reports list the measured coverage and remaining gaps per library; they impose no arbitrary coverage threshold.

To collect the same artifacts locally, install Xdebug and configure all three test services, then run:

```sh
XDEBUG_MODE=coverage php -d date.timezone=UTC -d memory_limit=2G vendor/bin/pest \
  --log-junit=reports/junit.xml --coverage-clover=reports/clover.xml
```

Passly and Marveld were read as compatibility examples. The full Passly suite was subsequently made runnable against the local Raxos libraries and passed 1,312 tests with 6,825 assertions; [the recorded run](reports/passly-suite.json) lists its environment. Marveld's application suite was not run. [MIGRATION.md](MIGRATION.md) lists the consumer changes required before upgrading.

The supplied Passly products GET request was reproduced and checked after the pagination fixes: HTTP 200, JSON output and matching item/pagination counts. The saved evidence omits credentials and product data. Synthetic database and search tests cover its query/filter/visibility path.

## Reproduce the performance measurements

```sh
RAXOS_REDIS_HOST=127.0.0.1 RAXOS_REDIS_PORT=6379 php tools/benchmark.php > /tmp/raxos-performance.json
```

The script measures lexer growth, suffix-list reloads, route registration, isolated JSON parsing, batched SQLite hydration, retained identities, local file sending and tagged Redis invalidation. CPU timings are medians after warmup. The Redis flush is a single run. File timing excludes process startup and network transfer. Results are machine dependent; query counts, batch sizes and retained identities give more stable regression signals.

The original review evidence remains in `reports/review-evidence.json`. Measurements after the changes are in `reports/performance-after.json`; both HTML reports distinguish those snapshots.

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
