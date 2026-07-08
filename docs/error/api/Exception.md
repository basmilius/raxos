---
outline: deep
---

# Exception

`Raxos\Error\Exception`

Abstract base class for all typed Raxos exceptions. It pairs a machine readable error code with a
human readable description, accepts an optional previous throwable, and serializes cleanly to JSON so
it can be returned directly in an API error response. Every other Raxos package extends this class
for its own exceptions.

```php
abstract class Exception extends \Exception implements ExceptionInterface
```

The class implements the `ExceptionInterface` contract from [raxos/contract](/contract/), which
extends `JsonSerializable`.

## Properties

Constructor property promotion exposes the following public, readonly properties:

- `string $error`: the machine readable error code.
- `string $errorDescription`: the human readable description.
- `?Throwable $previous`: the previous throwable, if any.

## Methods

### `__construct()`

```php
public function __construct(
    string $error,
    string $errorDescription,
    BackedEnum|ExceptionId|null $code = null,
    ?Throwable $previous = null
)
```

Creates the exception. When `$code` is omitted, a stable `ExceptionId` is derived from the concrete
class name through `ExceptionId::for(static::class)`. The resolved code value is passed to the native
`\Exception` constructor, so it is also available through `getCode()`.

### `jsonSerialize()`

```php
public function jsonSerialize(): array
```

Returns an array with `code`, `error` and `error_description`. When the previous throwable implements
`JsonSerializable`, it is added under a `previous` key.

## Usage

```php
<?php
declare(strict_types=1);

namespace App\Error;

use Raxos\Error\Exception;

final class OrderNotFoundException extends Exception
{
    public function __construct(int $orderId)
    {
        parent::__construct(
            error: 'order_not_found',
            errorDescription: "No order exists with id {$orderId}."
        );
    }
}

$exception = new OrderNotFoundException(7);

echo json_encode($exception);
// {"code":...,"error":"order_not_found","error_description":"No order exists with id 7."}
```

## See also

- [ExceptionId](/error/api/ExceptionId): the stable identifier used when `$code` is omitted.
- [InvalidArgumentException](/error/api/InvalidArgumentException): a concrete subclass.
- [Building custom exceptions](/error/custom-exceptions).
