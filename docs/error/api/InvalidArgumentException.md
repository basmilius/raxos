---
outline: deep
---

# InvalidArgumentException

`Raxos\Error\InvalidArgumentException`

A ready to use exception for invalid argument errors. It is the minimal, concrete example of the
`Exception` pattern: it fixes the error code to `invalid_argument` and reports the message you pass.

```php
final class InvalidArgumentException extends Exception
```

## Methods

### `__construct()`

```php
public function __construct(string $message)
```

Creates the exception with the given message as its error description and the fixed error code
`invalid_argument`. Because no code is passed to the parent constructor, a stable
[ExceptionId](/error/api/ExceptionId) is derived from the class name automatically.

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\Error\InvalidArgumentException;

function withLimit(int $limit): void
{
    if ($limit < 1) {
        throw new InvalidArgumentException('The provided limit must be positive.');
    }
}
```

## See also

- [Exception](/error/api/Exception): the abstract base class.
- [Building custom exceptions](/error/custom-exceptions).
