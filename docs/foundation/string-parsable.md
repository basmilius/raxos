---
outline: deep
---

# String parsable contract

`Raxos\Foundation\Contract\StringParsableInterface` lets a value object declare how it parses itself from a string and what pattern valid input matches. A value object that implements this contract can be recognised generically by consuming code, without that code keeping a hardcoded list of supported types.

The interface lives in the `Raxos\Foundation\Contract` namespace, alongside `OptionInterface` (documented on the [Option type](/foundation/option) page). This page fills the missing half of that namespace.

## The contract

```php
<?php
declare(strict_types=1);

namespace Raxos\Foundation\Contract;

use Stringable;

interface StringParsableInterface extends Stringable
{
    public static function fromString(string $input): static;

    public static function pattern(): string;
}
```

- The interface extends `Stringable`, so every implementer also provides `__toString()`. A value can therefore round-trip: build it from a string and cast it back to one.
- `fromString(string $input): static` builds an instance from a raw string.
- `pattern(): string` returns a regular expression fragment that describes valid input for the type.

## Implementing the contract

Any value object can opt in by implementing the two static methods and `__toString()`:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Contract\StringParsableInterface;

final readonly class HexColor implements StringParsableInterface
{
    public function __construct(
        public string $value
    ) {}

    public static function fromString(string $input): static
    {
        return new self(ltrim($input, '#'));
    }

    public static function pattern(): string
    {
        return '#?[0-9a-fA-F]{6}';
    }

    public function __toString(): string
    {
        return '#' . $this->value;
    }
}
```

The `pattern()` fragment is meant to be embedded in a larger expression by the consumer, so it describes the shape of a single value rather than a fully anchored regex.

## Who consumes it

The contract exists so other Raxos packages can accept string-parsable value objects generically. Two concrete consumers live in the monorepo:

- [Router](/router/) inspects the type of a route path parameter. When that type is a class implementing this contract (`is_subclass_of($type, StringParsableInterface::class)`), the router calls `pattern()` to build the regular expression for the parameter and `fromString()` to turn the matched segment into an instance.
- [DateTime](/datetime/) ships the `Date`, `Time` and `DateTime` value objects, all of which implement this contract. That is what lets them be parsed directly from route parameters or other string input.

Because the contract only depends on `Stringable`, an implementer is free to also compose with the rest of Foundation, such as the [Access traits](/foundation/access-traits) or the [Option type](/foundation/option). The interface itself simply lives in the Contract namespace next to `OptionInterface`.
