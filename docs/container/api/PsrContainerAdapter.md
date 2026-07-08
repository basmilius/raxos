---
outline: deep
---

# PsrContainerAdapter

`Raxos\Container\PsrContainerAdapter`

A final readonly adapter that exposes a Raxos `ContainerInterface` as a standard PSR-11 `Psr\Container\ContainerInterface`. Use it to hand the container to third party libraries that expect the PSR-11 contract.

```php
final readonly class PsrContainerAdapter implements Psr\Container\ContainerInterface
```

::: info
This class requires the optional `psr/container` package. See [installation](/container/installation).
:::

## Constructor

```php
public function __construct(
    public ContainerInterface $container,
)
```

Wraps an existing Raxos container.

## Methods

### get

```php
public function get(string $id): object
```

Resolves an entry from the wrapped container. If resolution fails and the container does not have the identifier, the failure is translated into a PSR-11 `NotFoundException`. Any other container failure is rethrown as is.

### has

```php
public function has(string $id): bool
```

Delegates directly to the wrapped container and reports whether it has an entry for the identifier.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Container\{Container, PsrContainerAdapter};

$container = new Container();
$container->singleton(LoggerInterface::class, FileLogger::class);

$psr = new PsrContainerAdapter($container);

$psr->has(LoggerInterface::class); // true
$logger = $psr->get(LoggerInterface::class);
```
