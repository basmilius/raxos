---
outline: deep
---

# TwoFactorAuthAlgorithm

`Raxos\Security\TwoFactor\TwoFactorAuthAlgorithm` enumerates the HMAC hashing algorithms usable for TOTP code generation by [`TwoFactorAuth`](/security/api/TwoFactorAuth).

```php
enum TwoFactorAuthAlgorithm: string
```

## Cases

| Case     | Value    |
|----------|----------|
| `SHA1`   | `sha1`   |
| `SHA256` | `sha256` |
| `SHA512` | `sha512` |
| `MD5`    | `md5`    |

`SHA1` is the common default and offers the broadest compatibility with authenticator apps. Match the value to whatever the app on the other side expects.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\TwoFactor\{TwoFactorAuth, TwoFactorAuthAlgorithm};

$totp = new TwoFactorAuth(
    issuer: 'Passly',
    algorithm: TwoFactorAuthAlgorithm::SHA256,
);
```
