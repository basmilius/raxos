---
outline: deep
---

# Option / Some / None

The Option type models an optional value. `Option` is the abstract base class with the static factories, `Some` holds a value, and `None` represents the absence of one. All three implement `Raxos\Foundation\Contract\OptionInterface`.

See the [Option type concept page](/foundation/option) for a guided introduction.

## Option

```php
namespace Raxos\Foundation\Option;

abstract readonly class Option implements OptionInterface
```

Base class for optional values, extended by `Some` and `None`. It provides the static factories used to build options.

### Static methods

```php
public static function some(mixed $value): OptionInterface
```
Returns a new `Some` wrapping the given value.

```php
public static function none(): OptionInterface
```
Returns the shared `None` instance.

```php
public static function fromValue(mixed $value, mixed $none = null): OptionInterface
```
Wraps a value in `Some`, or returns `None` if the value is identical to `$none`. If the value is already an option, it is returned as is.

```php
public static function fromCallable(callable $fn, mixed $none = null): OptionInterface
```
Calls the function and wraps the result via `fromValue()`.

## OptionInterface

The contract implemented by every option. Each method behaves differently for `Some` and `None`.

```php
public function get(): mixed;
public function getOrElse(mixed $fallback): mixed;
public function getOrInvoke(callable $fn): mixed;
public function getOrThrow(Throwable|callable $err): mixed;
public function map(callable $map): OptionInterface;
public function filter(callable $predicate): OptionInterface;
public function accept(mixed $value): OptionInterface;
public function reject(mixed $value): OptionInterface;
public function orElse(OptionInterface|callable $fallback): OptionInterface;
public function orThrow(Throwable|callable $err): OptionInterface;
```

## Some

```php
namespace Raxos\Foundation\Option;

final readonly class Some extends Option implements DebuggableInterface
```

Represents an option that holds a value. Every accessor returns the wrapped value.

```php
public function get(): mixed
```
Returns the wrapped value.

```php
public function map(callable $map): Option
```
Applies the callable to the value and wraps the result in a new `Some`.

```php
public function filter(callable $predicate): OptionInterface
```
Returns itself if the predicate is true, otherwise `None`.

## None

```php
namespace Raxos\Foundation\Option;

final readonly class None extends Option implements DebuggableInterface
```

Represents the absence of a value. Most accessors throw or fall back to a supplied default. `None` is created and shared through [Singleton](/foundation/api/Singleton), so every `Option::none()` returns the same object.

```php
public function get(): mixed
```
Throws `OptionException::noValue()`.

```php
public function getOrElse(mixed $fallback): mixed
```
Returns the given fallback value.

```php
public function getOrThrow(Throwable|callable $err): mixed
```
Throws the given exception, or the result of the given callable.

## OptionException

```php
namespace Raxos\Foundation\Option;

final class OptionException extends Exception
```

- `OptionException::noValue()`: thrown by `None::get()`.
- `OptionException::notAnOption()`: thrown by `None::orElse()` when a fallback callable does not return an `OptionInterface`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Option\Option;

function findUser(int $id): OptionInterface
{
    return Option::fromValue(lookup($id));
}

$name = findUser(1)
    ->map(static fn(array $user): string => $user['name'])
    ->getOrElse('Guest');
```
