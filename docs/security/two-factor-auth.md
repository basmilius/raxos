---
outline: deep
---

# Two factor authentication

The `Raxos\Security\TwoFactor` namespace implements TOTP based two factor authentication, the scheme used by authenticator apps such as Google Authenticator and 1Password. [`TwoFactorAuth`](/security/api/TwoFactorAuth) covers the full flow: secret generation, QR provisioning, code generation and verification. [`TwoFactorAuthAlgorithm`](/security/api/TwoFactorAuthAlgorithm) selects the hashing algorithm.

## Configuration

`TwoFactorAuth` is configured through its constructor: an issuer name, the number of digits in a code, the time step in seconds and the hashing algorithm. The defaults match what most authenticator apps expect: six digits, a 30 second period and `SHA1`.

```php
<?php
declare(strict_types=1);

use Raxos\Security\TwoFactor\{TwoFactorAuth, TwoFactorAuthAlgorithm};

$totp = new TwoFactorAuth(
    issuer: 'Passly',
    digits: 6,
    period: 30,
    algorithm: TwoFactorAuthAlgorithm::SHA1,
);
```

The constructor throws an `InvalidArgumentException` when `digits` or `period` is not a positive integer.

## Enrollment

During enrollment, generate a secret and hand it to the user's device. `createSecret` returns a base32 string with the requested amount of entropy in bits (160 by default). `generateQrData` turns that secret into an `otpauth://` URL that authenticator apps can scan as a QR code.

```php
$secret = $totp->createSecret();
$qrData = $totp->generateQrData($secret, 'user@example.com');
```

The `$label` argument identifies the account inside the authenticator app, commonly the user's email address. Render `$qrData` as a QR code, for example with [raxos/barcode](/barcode/), and store `$secret` alongside the user so codes can be verified later.

## Verifying a code

When the user submits a code, `verifyCode` checks it against a window of time steps around the current time. The `discrepancy` argument controls how many steps before and after now are accepted, which tolerates small clock drift between the server and the user's device. The default of `1` allows one step in each direction.

```php
$isValid = $totp->verifyCode($secret, $submittedCode);
```

## Generating a code

`generateCode` produces the one time password for a secret, either for the current time or for a specific timestamp. This is mostly useful for testing or for server side code delivery.

```php
$code = $totp->generateCode($secret);
$codeAtTime = $totp->generateCode($secret, 1704067200);
```

## Algorithms

`TwoFactorAuthAlgorithm` selects the HMAC hashing algorithm used to derive codes. The available cases are `SHA1` (the common default), `SHA256`, `SHA512` and `MD5`. Match the value to whatever the authenticator app on the other side expects; `SHA1` is the safe choice for broad compatibility.

## Exceptions

Besides the `InvalidArgumentException` from the constructor, two exceptions come out of the runtime methods, both implementing `TwoFactorAuthExceptionInterface` from [raxos/contract](/contract/):

- `createSecret` throws `TwoFactorAuthRandomizerException` when the system's CSPRNG fails to produce a strong enough random value.
- `generateCode` and `verifyCode` throw `TwoFactorAuthInvalidDataException` when the given secret is not valid base32, for example a corrupted or tampered stored secret.
