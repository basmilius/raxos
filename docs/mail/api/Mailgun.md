---
outline: deep
---

# Mailgun

`Raxos\Mail\Mailgun` sends a [`Mail`](/mail/api/Mail) through the Mailgun HTTP API. It implements `MailerInterface` from [raxos/contract](/contract/).

```php
final readonly class Mailgun implements MailerInterface
```

## Constructor

```php
public function __construct(
    #[SensitiveParameter] public string $apiKey,
    #[SensitiveParameter] public string $domain,
    #[SensitiveParameter] public string $endpoint = 'https://api.eu.mailgun.net'
)
```

Creates the provider and its underlying Mailgun client. The `$endpoint` defaults to the EU region. All three parameters are marked `#[SensitiveParameter]`, so they are redacted from stack traces.

## Methods

### `send`

```php
public function send(Mail $mail): bool
```

Builds the message with the sender, subject, HTML and text bodies, groups recipients into to, cc and bcc addresses, and adds each [`Attachment`](/mail/api/Attachment) as a string attachment. When [`isTesting()`](/foundation/) reports a testing environment, it enables Mailgun's own test mode. It throws `MailerFailedException` on client or limit errors from the Mailgun SDK.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\Mailgun;

$mailer = new Mailgun(
    apiKey: 'key-...',
    domain: 'mg.example.com',
);

$mailer->send($mail);
```
