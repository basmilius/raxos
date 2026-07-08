---
outline: deep
---

# SMTP

`Raxos\Mail\SMTP` sends a [`Mail`](/mail/api/Mail) over SMTP using PHPMailer. It implements `MailerInterface` from [raxos/contract](/contract/).

```php
final readonly class SMTP implements MailerInterface
```

## Constructor

```php
public function __construct(
    #[SensitiveParameter] public string $host,
    #[SensitiveParameter] public int $port = 587,
    #[SensitiveParameter] public string $username = '',
    #[SensitiveParameter] public string $password = '',
    public string $helo = '',
    public string $hostname = ''
)
```

Creates the provider with the SMTP connection settings. The `$host`, `$port`, `$username` and `$password` parameters are marked `#[SensitiveParameter]`, so they are redacted from stack traces. Every parameter except the host has a sensible default.

## Methods

### `send`

```php
public function send(Mail $mail): bool
```

Configures a PHPMailer instance for authenticated SMTP with `SMTPAuth` enabled, a UTF-8 charset and base64 encoding, then sends the message with its sender, recipients (grouped into to, cc and bcc) and attachments. When [`isTesting()`](/foundation/) reports a testing environment, it short circuits and returns `true` without sending. It throws `MailerFailedException` when PHPMailer throws or reports failure.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\SMTP;

$mailer = new SMTP(
    host: 'smtp.example.com',
    port: 587,
    username: 'mailer@example.com',
    password: 'secret',
);

$mailer->send($mail);
```
