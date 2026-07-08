---
outline: deep

cards:
    highlights:
        -   title: MailerInterface
            code: true
            details: 'One send() contract shared by the Mailgun, Postmark and SMTP providers.'
            link: /mail/sending-mail
        -   title: Mail
            code: true
            details: 'An immutable envelope bundling subject, bodies, sender, recipients and attachments.'
            link: /mail/api/Mail
        -   title: Email
            code: true
            details: 'A validated address value object that parses username, domain and an optional plus tag.'
            link: /mail/api/Email
        -   title: EmailSuggester
            code: true
            details: 'Detects likely typos in a domain and proposes corrected addresses.'
            link: /mail/api/EmailSuggester
---

# Mail

Raxos Mail is a small mailing library built around a single `MailerInterface` contract. You compose a message with plain immutable value objects (`Mail`, `Sender`, `Recipient`, `Attachment`), then hand it to one of three ready made providers: Mailgun, Postmark or SMTP. Because every provider accepts the same `Mail` envelope, swapping one for another is just a matter of instantiating a different class. Nothing else in your code needs to change.

Alongside the sending pipeline, the package ships an `Email` value object that validates and splits an address into its parts, and an `EmailSuggester` that spots likely typos in a domain and proposes corrections.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Composing a mail](/mail/composing-mail): the `Mail` envelope and its building blocks, `Sender`, `Recipient`, `RecipientType` and `Attachment`.
- [Sending mail](/mail/sending-mail): the `MailerInterface` contract, the three built in providers and how failures surface.
- [Email addresses and suggestions](/mail/email-addresses): parsing and validating addresses with `Email`, and detecting typos with `EmailSuggester`.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\{Mail, Postmark, Recipient, Sender};

$mail = new Mail(
    subject: 'Welcome!',
    html: '<p>Thanks for signing up.</p>',
    text: 'Thanks for signing up.',
    sender: new Sender('hello@example.com', 'Example'),
    recipients: [
        new Recipient('user@example.com', 'Jane Doe'),
    ],
);

$mailer = new Postmark(apiKey: 'server-token');
$mailer->send($mail);
```

## Installation

Install the package with Composer. See [installation](/mail/installation) for the required PHP version and dependencies.

```shell
composer require raxos/mail
```
