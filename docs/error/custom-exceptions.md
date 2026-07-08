---
outline: deep
---

# Building custom exceptions

Every exception in the Raxos ecosystem extends the abstract `Exception` class. Modeling your own
application specific exceptions on top of it gives you a consistent shape: a machine readable error
code, a human readable description, an optional previous throwable, and a stable numeric identifier,
all serializable to JSON.

## Extend the base class

Create a `final` class that extends `Exception` and forwards the relevant values to its constructor.
The constructor accepts an `error` code string, an `errorDescription`, an optional `code` (a
`BackedEnum` or an [ExceptionId](/error/api/ExceptionId)), and an optional previous throwable.

```php
<?php
declare(strict_types=1);

namespace App\Error;

use Raxos\Error\Exception;

final class UserNotFoundException extends Exception
{
    public function __construct(string $userId)
    {
        parent::__construct(
            error: 'user_not_found',
            errorDescription: "No user exists with id {$userId}."
        );
    }
}

throw new UserNotFoundException('42');
```

The `error` code is the stable, machine readable identifier, while `errorDescription` carries the
human readable detail.

## Let the identifier be derived automatically

Leave `$code` as `null` and the constructor calls `ExceptionId::for(static::class)` for you. The
identifier is a CRC32 hash of the concrete class name, so it stays the same across requests and
deployments without you assigning it by hand.

To use your own value instead, pass a `BackedEnum` case or an explicit `ExceptionId`.

```php
<?php
declare(strict_types=1);

namespace App\Error;

use Raxos\Error\{Exception, ExceptionId};

final class PaymentFailedException extends Exception
{
    public function __construct(string $reason)
    {
        parent::__construct(
            error: 'payment_failed',
            errorDescription: $reason,
            code: new ExceptionId(4001)
        );
    }
}
```

## Derive an identifier from the call site

Inside a method that throws, `ExceptionId::guess()` inspects the current debug backtrace and derives
an identifier from the calling method or function. This is convenient when you want a per call site
identifier without naming the class explicitly.

```php
<?php
declare(strict_types=1);

namespace App\Service;

use Raxos\Error\{Exception, ExceptionId};

final class TokenExpiredException extends Exception
{
    public function __construct(ExceptionId $code)
    {
        parent::__construct(
            error: 'token_expired',
            errorDescription: 'The provided token has expired.',
            code: $code
        );
    }
}

final class TokenValidator
{
    public function validate(string $token): void
    {
        throw new TokenExpiredException(ExceptionId::guess());
    }
}
```

## Serialize straight to an API response

`Exception` implements `JsonSerializable`, so an instance can be returned or encoded directly. The
`jsonSerialize()` method exposes `code`, `error` and `error_description`, and adds a `previous` entry
when the previous throwable is itself a `JsonSerializable`.

```php
$exception = new UserNotFoundException('42');

echo json_encode($exception);
// {"code":123456789,"error":"user_not_found","error_description":"No user exists with id 42."}
```

## The simplest possible example

The built in [InvalidArgumentException](/error/api/InvalidArgumentException) shows the pattern in its
minimal form: a `final` class that only supplies a fixed `invalid_argument` error code and a message.

```php
<?php
declare(strict_types=1);

use Raxos\Error\InvalidArgumentException;

throw new InvalidArgumentException('The provided limit must be positive.');
```
