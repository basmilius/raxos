---
outline: deep
---

# Ulid

`Raxos\Security\Id\Ulid` represents a Universally Unique Lexicographically Sortable Identifier, made of a time part and a randomness part. It implements `Stringable`, so an instance can be cast to a string directly.

```php
final class Ulid implements Stringable
```

## Methods

### `__construct`

```php
public function __construct(
    public readonly string $time,
    public readonly string $randomness,
    public readonly bool $lowercase = false
)
```

Creates a ULID from its raw time and randomness parts. Pass `$lowercase` to emit the identifier in lower case when cast to a string.

### `fromString`

```php
public static function fromString(string $value, bool $lowercase = false): self
```

Parses an existing ULID string. Throws `UlidInvalidLengthException` when the length is wrong and `UlidWrongCharactersException` when the value contains characters outside the ULID alphabet.

### `fromTimestamp`

```php
public static function fromTimestamp(int $milliseconds, bool $lowercase = false): self
```

Builds a ULID for the given millisecond timestamp, using monotonic randomness for repeated calls in the same millisecond.

### `generate`

```php
public static function generate(bool $lowercase = false): self
```

Generates a new ULID for the current time.

### `toTimestamp`

```php
public function toTimestamp(): int
```

Extracts the millisecond timestamp encoded in the ULID. Throws `UlidWrongCharactersException` for an invalid time part and `UlidTimestampTooLargeException` when the decoded value exceeds the maximum.

### `__toString`

```php
public function __toString(): string
```

Returns the 26 character ULID string, in upper case by default or lower case when the instance was created with `$lowercase` set.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\Id\Ulid;

$ulid = Ulid::generate();
$asString = (string)$ulid;
$createdAt = $ulid->toTimestamp();

$parsed = Ulid::fromString($asString);
```
