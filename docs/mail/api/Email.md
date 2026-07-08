---
outline: deep
---

# Email

`Raxos\Mail\Email` is a validated email address split into a `username`, a `domain` and an optional plus `tag`. It implements both `JsonSerializable` and `Stringable`.

```php
final readonly class Email implements JsonSerializable, Stringable
```

## Constructor

```php
public function __construct(
    public string $username,
    public string $domain,
    public ?string $tag = null
)
```

Builds an `Email` from already separated parts. Most callers use [`fromString`](#fromstring) instead.

## Methods

### `fromString`

```php
public static function fromString(string $email): self
```

Parses and validates a raw address string. It requires exactly one `@` and validates with `FILTER_VALIDATE_EMAIL`. When the local part contains a `+`, the text after it becomes the `tag`. Throws `InvalidEmailAddressException` (which implements `EmailAddressExceptionInterface` from [raxos/contract](/contract/)) for anything that is not a single valid address.

### `jsonSerialize`

```php
public function jsonSerialize(): string
```

Serializes to the same string as `__toString`.

### `__toString`

```php
public function __toString(): string
```

Renders as `username@domain`, or `username+tag@domain` when a tag is present.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\Email;

$email = Email::fromString('jane+newsletter@example.com');

$email->username; // 'jane'
$email->domain;   // 'example.com'
$email->tag;      // 'newsletter'

echo (string)$email;
// jane+newsletter@example.com
```

See [Email addresses and suggestions](/mail/email-addresses) for more.
