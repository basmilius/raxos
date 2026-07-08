---
outline: deep
---

# Identity

`Raxos\Wallet\Apple\Identity` is the readonly value object carrying the signing credentials for a pass issuer.

```php
final readonly class Identity
```

## Constructor

| Parameter            | Type     | Description                                              |
|----------------------|----------|----------------------------------------------------------|
| `certificate`        | `string` | The pass type identifier certificate in PEM form.        |
| `privateKey`         | `string` | The private key for the certificate in PEM form.         |
| `password`           | `string` | The password protecting the private key.                 |
| `passTypeIdentifier` | `string` | The pass type identifier, for example `pass.dev.example`.|
| `teamIdentifier`     | `string` | The Apple Developer team identifier.                     |

All five values are public properties.

## Usage

An `Identity` is passed once to the [PKPass](/wallet/api/PKPass) constructor and reused for every pass signed by the same issuer. During `PKPass::sign()`, the certificate, private key and password are handed to `openssl_pkcs7_sign`. When `PKPass` writes `pass.json`, it merges `passTypeIdentifier` and `teamIdentifier` into the pass data.

The values come from the [Apple Developer portal](https://developer.apple.com): a pass type identifier certificate exported together with its private key and password. See [installation](/wallet/installation) for what to obtain and where to place the WWDR certificate.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Identity;

$identity = new Identity(
    certificate: file_get_contents(__DIR__ . '/certificate.pem'),
    privateKey: file_get_contents(__DIR__ . '/private-key.pem'),
    password: 'secret',
    passTypeIdentifier: 'pass.dev.example.ticket',
    teamIdentifier: 'ABCDE12345'
);
```

## See also

- [Signing and packaging](/wallet/signing-and-packaging)
- [PKPass](/wallet/api/PKPass)
