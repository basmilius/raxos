---
outline: deep
---

# Option type

The Option type represents a value that may or may not be present, without relying on `null`. It is a functional alternative to nullable return types: an option is either `Some`, holding a value, or `None`, holding nothing. Both implement the same interface, so you can chain transformations and only decide how to handle the empty case at the very end.

The relevant classes live in the `Raxos\Foundation\Option` namespace, and the contract is `Raxos\Foundation\Contract\OptionInterface`.

## Creating options

`Option` is the abstract base class with the static factories used to build instances:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Option\Option;

$some = Option::some(42);          // wraps a value in Some
$none = Option::none();            // the shared None instance

$fromValue = Option::fromValue($maybeNull);       // None when the value equals null
$fromValue = Option::fromValue($value, none: '');  // None when the value equals ''

$fromCallable = Option::fromCallable(static fn(): ?string => getenv('TOKEN') ?: null);
```

- `Option::some()` always returns a `Some` wrapping the value.
- `Option::none()` returns the shared `None` instance, backed by [Singleton](/foundation/singleton-and-stopwatch) so every `None` is the same object.
- `Option::fromValue()` returns `None` when the value is identical to the `$none` argument (default `null`), otherwise `Some`. If the value is already an option, it is returned as is.
- `Option::fromCallable()` calls the function and passes its result through `fromValue()`.

## Reading a value

`OptionInterface` exposes several ways to get at the value, differing in how they handle the empty case:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Option\{Option, OptionException};

$option = Option::some('hello');

$option->get();                  // 'hello'
$option->getOrElse('fallback');  // 'hello'
$option->getOrInvoke(fn() => compute());
$option->getOrThrow(new RuntimeException('missing'));

$empty = Option::none();

$empty->getOrElse('fallback');   // 'fallback'
$empty->getOrInvoke(fn() => 'computed'); // 'computed'
$empty->get();                   // throws OptionException::noValue()
$empty->getOrThrow(fn() => new RuntimeException('missing')); // throws it
```

For `Some`, every accessor returns the wrapped value. For `None`, `get()` and `getOrThrow()` throw, while `getOrElse()` and `getOrInvoke()` return the supplied fallback.

## Transforming and filtering

Options support chaining, so you can build a pipeline that short circuits on `None`:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Option\Option;

$result = Option::fromValue($rawInput)
    ->map(static fn(string $value): string => trim($value))
    ->filter(static fn(string $value): bool => $value !== '')
    ->map(static fn(string $value): string => strtoupper($value))
    ->getOrElse('EMPTY');
```

- `map()` applies a callable to the value and wraps the result in a new `Some`. On `None` it returns `None` unchanged.
- `filter()` returns the option if the predicate is true, otherwise `None`.
- `accept()` keeps the option only if the value equals the given value, otherwise `None`.
- `reject()` keeps the option only if the value differs from the given value, otherwise `None`.
- `orElse()` returns the option itself when it holds a value, otherwise the fallback option (or the option returned by a callable).
- `orThrow()` returns the option when it holds a value, otherwise throws the given exception.

## Failure cases

`OptionException` covers the two failure modes:

- `OptionException::noValue()` is thrown by `None::get()`.
- `OptionException::notAnOption()` is thrown by `None::orElse()` when a fallback callable returns something that is not an `OptionInterface`.

See the [Option API reference](/foundation/api/Option) for full method signatures.
