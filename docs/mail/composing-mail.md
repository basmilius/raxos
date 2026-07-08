---
outline: deep
---

# Composing a mail

A message is a plain, immutable value object. You assemble a [`Mail`](/mail/api/Mail) from a subject, an HTML body, a plain text body, a [`Sender`](/mail/api/Sender), a list of [`Recipient`](/mail/api/Recipient) objects and, optionally, a list of [`Attachment`](/mail/api/Attachment) objects. None of these touch the network or the filesystem, so building a `Mail` is cheap and side effect free. The provider you send it through does the rest.

## The Mail envelope

`Mail` bundles everything a provider needs to deliver a message. The constructor is fully typed and uses property promotion, so every field is a public readonly property once the object exists.

```php
<?php
declare(strict_types=1);

use Raxos\Mail\{Attachment, Mail, Recipient, Sender};

$mail = new Mail(
    subject: 'Your invoice',
    html: '<p>Please find your invoice attached.</p>',
    text: 'Please find your invoice attached.',
    sender: new Sender('billing@example.com', 'Example Billing'),
    recipients: [
        new Recipient('jane@example.com', 'Jane Doe'),
    ],
    attachments: [
        new Attachment('invoice.pdf', file_get_contents('/path/to/invoice.pdf')),
    ],
);
```

The `recipients` array holds `Recipient` objects and the `attachments` array holds `Attachment` objects. Both providers iterate over these lists, so you can pass as many as you need.

## Senders and recipients

A `Sender` carries a display name and an address. The address can be either a raw string or an [`Email`](/mail/api/Email) value object, whichever you already have on hand.

```php
use Raxos\Mail\{Email, Sender};

$fromString = new Sender('hello@example.com', 'Example');
$fromEmail = new Sender(Email::fromString('hello@example.com'), 'Example');
```

A `Recipient` works the same way, but adds a [`RecipientType`](#recipient-types) that tells the provider how to address it.

```php
use Raxos\Mail\{Recipient, RecipientType};

$recipients = [
    new Recipient('jane@example.com', 'Jane Doe'),
    new Recipient('team@example.com', 'The Team', RecipientType::CC),
    new Recipient('audit@example.com', 'Audit Log', RecipientType::BCC),
];
```

Both `Sender` and `Recipient` implement `Stringable` and render as `Name <email>`, which is exactly the format a mail header expects.

```php
echo (string)new Sender('hello@example.com', 'Example');
// Example <hello@example.com>
```

## Recipient types

`RecipientType` is a pure enum with three cases:

- `RecipientType::TO`, the default, addresses a direct recipient.
- `RecipientType::CC` adds a carbon copy recipient.
- `RecipientType::BCC` adds a blind carbon copy recipient.

Each provider groups recipients by this type before handing them to its client, so a single `recipients` array can mix all three.

## Attachments

An `Attachment` is a name paired with raw content. It does no filesystem access of its own: you read the file (or generate the bytes) and pass the result in.

```php
use Raxos\Mail\Attachment;

$attachment = new Attachment('report.csv', "id,name\n1,Jane\n");
```

Providers forward the name and content straight to their client, so an in memory string and a file you just read are treated the same way.
