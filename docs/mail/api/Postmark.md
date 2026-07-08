---
outline: deep
---

# Postmark

`Raxos\Mail\Postmark` sends a [`Mail`](/mail/api/Mail) through the Postmark API. It implements `MailerInterface` from [raxos/contract](/contract/).

```php
final readonly class Postmark implements MailerInterface
```

## Constructor

```php
public function __construct(
    #[SensitiveParameter] public string $apiKey
)
```

Creates the provider and its underlying Postmark client. The `$apiKey` is marked `#[SensitiveParameter]`, so it is redacted from stack traces.

## Methods

### `send`

```php
public function send(Mail $mail): bool
```

Groups recipients into separate to, cc and bcc lists (each rendered as `Name <email>`) and forwards every [`Attachment`](/mail/api/Attachment) as a `PostmarkAttachment`. When [`isTesting()`](/foundation/) reports a testing environment, it short circuits and returns `true` without sending. It throws `MailerFailedException` when the Postmark SDK reports a failure.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\Postmark;

$mailer = new Postmark(apiKey: 'server-token');

$mailer->send($mail);
```
