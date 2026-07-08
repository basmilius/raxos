---
outline: deep
---

# Sender

`Raxos\Mail\Sender` is the from address and display name of a [`Mail`](/mail/api/Mail). It implements `Stringable` and renders as `Name <email>`.

```php
final readonly class Sender implements Stringable
```

## Constructor

```php
public function __construct(
    public Email|string $email,
    public string $name
)
```

The `$email` property accepts either an [`Email`](/mail/api/Email) instance or a plain string, so you can pass whichever you already have.

## Methods

### `__toString`

```php
public function __toString(): string
```

Renders the sender as `Name <email>`, the format a mail header expects.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\{Email, Sender};

$sender = new Sender('hello@example.com', 'Example');
echo (string)$sender;
// Example <hello@example.com>

$fromEmail = new Sender(Email::fromString('hello@example.com'), 'Example');
```
