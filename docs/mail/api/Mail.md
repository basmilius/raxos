---
outline: deep
---

# Mail

`Raxos\Mail\Mail` is the immutable envelope of a message: a subject, an HTML and a plain text body, a sender, a list of recipients and an optional list of attachments. You pass it straight to [`MailerInterface::send`](/mail/sending-mail).

```php
final readonly class Mail
```

## Constructor

```php
public function __construct(
    public string $subject,
    public string $html,
    public string $text,
    public Sender $sender,
    public array $recipients,
    public array $attachments = []
)
```

`$recipients` is an array of [`Recipient`](/mail/api/Recipient) objects and `$attachments` is an array of [`Attachment`](/mail/api/Attachment) objects. Every argument becomes a public readonly property.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\{Mail, Recipient, Sender};

$mail = new Mail(
    subject: 'Welcome!',
    html: '<p>Thanks for signing up.</p>',
    text: 'Thanks for signing up.',
    sender: new Sender('hello@example.com', 'Example'),
    recipients: [
        new Recipient('user@example.com', 'Jane Doe'),
    ],
);
```

See [Composing a mail](/mail/composing-mail) for a fuller walkthrough.
