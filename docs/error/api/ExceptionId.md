---
outline: deep
---

# ExceptionId

`Raxos\Error\ExceptionId`

Wraps a numeric identifier that uniquely and reproducibly identifies an exception origin, either a
class or a method. It lets exceptions be tracked without manually assigning codes.

```php
final readonly class ExceptionId implements JsonSerializable
```

## Properties

- `int $value`: the wrapped numeric identifier.

## Methods

### `__construct()`

```php
public function __construct(int $value)
```

Wraps an existing numeric identifier.

### `for()`

```php
public static function for(string $methodOrClassName): self
```

Derives a stable identifier from a class or method name. The name is hashed with CRC32 and the
hexadecimal result is converted to a base 10 integer, so the same name always yields the same
identifier.

### `guess()`

```php
public static function guess(): self
```

Derives an identifier from the caller found in the current debug backtrace. It resolves the calling
method or function and delegates to `for()`. This is useful when called directly inside the throwing
method, so the identifier reflects the exact call site.

### `jsonSerialize()`

```php
public function jsonSerialize(): int
```

Returns the raw integer value.

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\Error\ExceptionId;

// Stable, derived from a class name.
$byClass = ExceptionId::for(App\Service\PaymentService::class);

// Explicit, hand assigned value.
$explicit = new ExceptionId(4001);

// Derived from the current call site.
$fromCallSite = ExceptionId::guess();
```

## See also

- [Exception](/error/api/Exception): uses `ExceptionId::for()` when no code is supplied.
- [Building custom exceptions](/error/custom-exceptions).
