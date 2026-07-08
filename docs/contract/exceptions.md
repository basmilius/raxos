---
outline: deep
---

# Exception contracts

The exception interface hierarchy is the part of Contract that application code touches most often. It gives every Raxos exception a common shape and lets you catch whole families of errors by their interface rather than by listing every concrete class.

## The root: ExceptionInterface

Every Raxos exception ultimately implements [`ExceptionInterface`](/contract/api/ExceptionInterface). It extends PHP's `Throwable` and `JsonSerializable`, and it adds two readable properties:

- `$error`: a stable, machine readable identifier such as `database_connection_failed`.
- `$errorDescription`: a human readable description of what went wrong.

Because it extends `JsonSerializable`, an exception can be serialized straight into an API error response.

## A marker interface per package

Each package defines its own marker interface that extends `ExceptionInterface` with no extra members. Examples include `ContainerExceptionInterface`, `DatabaseExceptionInterface`, `CacheExceptionInterface` and `RouterExceptionInterface`. They exist purely so you can catch every exception from one package with a single type.

```php
<?php
declare(strict_types=1);

namespace Raxos\Contract\Container;

use Raxos\Contract\ExceptionInterface;

interface ContainerExceptionInterface extends ExceptionInterface {}
```

Some marker interfaces extend another domain's interface rather than the root directly. For example `OrmExceptionInterface` extends `DatabaseExceptionInterface`, reflecting that an ORM error is also a database error:

```php
<?php
declare(strict_types=1);

namespace Raxos\Contract\Database\Orm;

use Raxos\Contract\Database\DatabaseExceptionInterface;

interface OrmExceptionInterface extends DatabaseExceptionInterface {}
```

## Where the concrete classes live

Contract holds only the interfaces. The concrete exception classes that implement them live in [raxos/error](/error/), whose base `Exception` class implements `ExceptionInterface`. That means a single `catch` against the interface handles a whole family of exceptions without naming every concrete class.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Database\DatabaseExceptionInterface;

try {
    $user = $connection->query()
        ->select()
        ->from('users')
        ->single();
} catch (DatabaseExceptionInterface $exception) {
    // Handles connection errors, query errors, ORM errors and more,
    // because they all extend DatabaseExceptionInterface.
    error_log($exception->error . ': ' . $exception->errorDescription);
}
```

## Defining your own

You can follow the exact same pattern in your application: a small marker interface per domain that extends the root `ExceptionInterface`, implemented by concrete exceptions that extend raxos/error's base `Exception`.

```php
<?php
declare(strict_types=1);

namespace App\Billing;

use Raxos\Contract\ExceptionInterface;
use Raxos\Error\Exception;

interface BillingExceptionInterface extends ExceptionInterface {}

final class InvoiceLockedException extends Exception implements BillingExceptionInterface
{
    public function __construct(string $invoiceId)
    {
        parent::__construct(
            error: 'invoice_locked',
            errorDescription: "Invoice {$invoiceId} is locked and cannot be modified.",
        );
    }
}
```
