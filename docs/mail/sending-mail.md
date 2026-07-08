---
outline: deep
---

# Sending mail

Every provider implements the same contract, so the code that sends a message never depends on which provider is behind it. You build a [`Mail`](/mail/api/Mail), then call `send()` on any implementation.

## The MailerInterface contract

`MailerInterface`, defined in [raxos/contract](/contract/), is deliberately tiny. It exposes a single method:

```php
public function send(Mail $mail): bool;
```

It returns `true` when the message was accepted, and throws a `MailerExceptionInterface` when sending fails. Any code you write against this interface works unchanged with all three providers.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Mail\MailerInterface;
use Raxos\Mail\Mail;

function notify(MailerInterface $mailer, Mail $mail): void
{
    $mailer->send($mail);
}
```

## Mailgun

[`Mailgun`](/mail/api/Mailgun) sends through the Mailgun HTTP API. Its constructor takes an API key, a domain and an optional endpoint that defaults to the EU region. The three connection parameters are marked `#[SensitiveParameter]`, so they are redacted from stack traces.

```php
use Raxos\Mail\Mailgun;

$mailer = new Mailgun(
    apiKey: 'key-...',
    domain: 'mg.example.com',
    endpoint: 'https://api.eu.mailgun.net',
);

$mailer->send($mail);
```

Recipients are grouped into to, cc and bcc addresses and attachments are added as string attachments. When a client or limit error comes back from the Mailgun SDK, the provider wraps it in a `MailerFailedException`.

## Postmark

[`Postmark`](/mail/api/Postmark) sends through the Postmark API and only needs a server API key.

```php
use Raxos\Mail\Postmark;

$mailer = new Postmark(apiKey: 'server-token');

$mailer->send($mail);
```

Under the hood it splits recipients into separate to, cc and bcc lists, renders each as `Name <email>`, and converts every [`Attachment`](/mail/api/Attachment) into a `PostmarkAttachment`. A `PostmarkException` from the SDK becomes a `MailerFailedException`.

## SMTP

[`SMTP`](/mail/api/SMTP) sends over a plain SMTP server using PHPMailer. The constructor takes a host, an optional port (default `587`), a username, a password, a HELO name and a hostname.

```php
use Raxos\Mail\SMTP;

$mailer = new SMTP(
    host: 'smtp.example.com',
    port: 587,
    username: 'mailer@example.com',
    password: 'secret',
);

$mailer->send($mail);
```

The provider configures PHPMailer for authenticated SMTP with a UTF-8 charset and base64 encoding. Any throwable from PHPMailer, or a failed send, is wrapped in a `MailerFailedException`.

## Error handling

All three providers funnel client failures through the same exception. `MailerFailedException` (in the `Raxos\Mail\Error` namespace) implements `MailerExceptionInterface` from [raxos/contract](/contract/) and extends the base `Exception` from [raxos/error](/error/). It keeps the original throwable as its `previous` so you can inspect the underlying cause.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Mail\MailerExceptionInterface;

try {
    $mailer->send($mail);
} catch (MailerExceptionInterface $err) {
    // logging, retry, or surface a friendly message
}
```

## Behavior under testing

The providers cooperate with the `isTesting()` helper from [raxos/foundation](/foundation/) so your test suite never sends real mail:

- `Postmark` and `SMTP` short circuit and return `true` immediately, without contacting the server.
- `Mailgun` enables Mailgun's own test mode on the outgoing message instead.

## Choosing a provider at runtime

Raxos does not ship a factory for picking a provider. A plain `match` against your own configuration is enough, and it keeps the return type as the shared interface.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Mail\MailerInterface;
use Raxos\Mail\{Mailgun, Postmark, SMTP};
use Raxos\Mail\Error\MailerInvalidProviderException;

$mailer = match ($config->provider) {
    'mailgun' => new Mailgun($config->apiKey, $config->domain),
    'postmark' => new Postmark($config->apiKey),
    'smtp' => new SMTP($config->host, $config->port, $config->username, $config->password),
    default => throw new MailerInvalidProviderException($config->provider),
};
```

The `default` arm keeps the match exhaustive: every branch either returns a `MailerInterface` or throws, so `$mailer` is always the shared interface type. See the section below for the exception it throws.

## MailerInvalidProviderException

`MailerInvalidProviderException` (in the `Raxos\Mail\Error` namespace) is the third exception the package defines, alongside `MailerFailedException` and `InvalidEmailAddressException`. Like the other two it implements `MailerExceptionInterface` from [raxos/contract](/contract/) and extends the base `Exception` from [raxos/error](/error/), so a single `catch (MailerExceptionInterface $err)` still covers it.

Unlike `MailerFailedException`, the providers never throw it themselves. It is meant for your own application code, in the spot where you turn a configured provider name into a concrete mailer. When the name matches none of the known providers, throw it from the `default` arm of your `match`, as shown above.

Its constructor takes the offending provider name and exposes it as a public `readonly` property, so a handler can report exactly which value was wrong:

```php
<?php
declare(strict_types=1);

use Raxos\Mail\Error\MailerInvalidProviderException;

try {
    $mailer = resolveMailer($config);
} catch (MailerInvalidProviderException $err) {
    // $err->provider holds the unknown provider name, for example 'sendgrid'.
    logger()->warning('Unknown mail provider configured', ['provider' => $err->provider]);

    throw $err;
}
```

The exception carries the error code `mailer_invalid_provider` and a description of the form `Provider {name} is not a valid provider.`.
