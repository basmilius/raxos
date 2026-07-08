---
outline: deep
---

# Installation

Install Mail with Composer.

```shell
composer require raxos/mail
```

## Requirements

- PHP 8.5 or newer.

The package has no required PHP extensions of its own.

## Provider clients

Mail bundles the official client library for each provider, so you do not need to require them separately:

- `mailgun/mailgun-php` backs the [Mailgun](/mail/api/Mailgun) provider.
- `wildbit/postmark-php` backs the [Postmark](/mail/api/Postmark) provider.
- `phpmailer/phpmailer` backs the [SMTP](/mail/api/SMTP) provider.

## Raxos dependencies

Mail builds on a few other Raxos packages, which Composer installs for you:

- [raxos/foundation](/foundation/): provides the `isTesting()` helper the providers use to short circuit sending during tests.
- [raxos/contract](/contract/): defines the public interfaces, `MailerInterface`, `MailerExceptionInterface` and `EmailAddressExceptionInterface`.
- [raxos/error](/error/): provides the base `Exception` class that every Mail exception extends.

Return to the [Mail introduction](/mail/).
