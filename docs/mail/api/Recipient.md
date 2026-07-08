---
outline: deep
---

# Recipient

`Raxos\Mail\Recipient` is one recipient of a [`Mail`](/mail/api/Mail), tagged with a `RecipientType` that classifies it as a direct, carbon copy or blind carbon copy target. It implements `Stringable` and renders as `Name <email>`.

Unlike the other value objects in the package, `Recipient` is a plain `readonly class` rather than `final`, so you can extend it if you need to.

```php
readonly class Recipient implements Stringable
```

## Constructor

```php
public function __construct(
    public Email|string $email,
    public string $name,
    public RecipientType $type = RecipientType::TO
)
```

The `$email` property accepts either an [`Email`](/mail/api/Email) instance or a plain string. The `$type` defaults to `RecipientType::TO`.

## Methods

### `__toString`

```php
public function __toString(): string
```

Renders the recipient as `Name <email>`. The Postmark provider uses this string directly when building its recipient lists.

## RecipientType

`Raxos\Mail\RecipientType` is a pure enum that classifies a recipient.

```php
enum RecipientType
{
    case TO;
    case CC;
    case BCC;
}
```

- `TO`: a direct recipient (the default).
- `CC`: a carbon copy recipient.
- `BCC`: a blind carbon copy recipient.

Each provider groups recipients by this type before handing them to its client.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\{Recipient, RecipientType};

$recipients = [
    new Recipient('jane@example.com', 'Jane Doe'),
    new Recipient('team@example.com', 'The Team', RecipientType::CC),
    new Recipient('audit@example.com', 'Audit Log', RecipientType::BCC),
];
```
