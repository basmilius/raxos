---
outline: deep

cards:
    highlights:
        -   title: ExceptionInterface
            code: true
            details: 'Root contract every Raxos exception implements, adding a machine readable code and description on top of Throwable.'
            link: /contract/api/ExceptionInterface
        -   title: ContainerInterface
            code: true
            details: 'The dependency injection contract you type hint against instead of the concrete Container class.'
            link: /contract/api/ContainerInterface
        -   title: CasterInterface
            code: true
            details: 'ORM extension point for translating a raw column value to and from a rich PHP value.'
            link: /contract/api/CasterInterface
        -   title: MiddlewareInterface
            code: true
            details: 'Router pipeline extension point that either returns a response or forwards to the next handler.'
            link: /contract/api/MiddlewareInterface
        -   title: HandlerInterface
            code: true
            details: 'Message bus extension point implemented once per message type dispatched through the bus or a queue.'
            link: /contract/api/HandlerInterface
        -   title: ArrayableInterface
            code: true
            details: 'The smallest collection contract, implemented by anything that can be reduced to a plain array.'
            link: /contract/api/ArrayableInterface
---

# Contract

Contract is a dependency free package of PHP interfaces that every other Raxos package implements or type hints against. It lets packages refer to each other's behavior without depending on each other's concrete classes, so modules stay decoupled, testable and swappable. It holds only interfaces, no concrete logic, and because it has no Raxos dependencies of its own it sits at the very bottom of the dependency graph and is safe to require from anywhere, including your own application code.

Its namespaces mirror the packages it describes. `Raxos\Contract\Database` describes [raxos/database](/database/), `Raxos\Contract\Router` describes [raxos/router](/router/), and so on. On top of those, a small set of root level interfaces (`ExceptionInterface`, `DebuggableInterface`, `SerializableInterface`) is shared by every package.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Package organization](/contract/organization): how the interfaces are grouped by namespace so you can find the contract for a given package quickly.
- [Exception contracts](/contract/exceptions): the shared exception interface hierarchy rooted in `ExceptionInterface`, the part of the package application code touches most often.
- [Extension points](/contract/extension-points): the interfaces you implement yourself to plug custom behavior into a Raxos package.

## Quick example

Type hint against a contract rather than a concrete class, so the implementation can be swapped or mocked without touching your code.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Container\ContainerInterface;

final readonly class ReportService
{
    public function __construct(
        private ContainerInterface $container,
    ) {}

    public function build(string $abstract): object
    {
        return $this->container->get($abstract);
    }
}
```

## Installation

Install the package with Composer and check the requirements on the [installation](/contract/installation) page.

```shell
composer require raxos/contract
```
