---
outline: deep
---

# TwoFactorAuth

`Raxos\Security\TwoFactor\TwoFactorAuth` implements TOTP based two factor authentication: secret generation, code generation and verification, and otpauth QR data.

```php
readonly class TwoFactorAuth
```

## Methods

### `__construct`

```php
public function __construct(
    public ?string $issuer = null,
    public int $digits = 6,
    public int $period = 30,
    public TwoFactorAuthAlgorithm $algorithm = TwoFactorAuthAlgorithm::SHA1
)
```

Configures the issuer name, code length, time step in seconds and hashing algorithm. Throws `InvalidArgumentException` when `$digits` or `$period` is not a positive integer.

### `createSecret`

```php
public function createSecret(int $bits = 160): string
```

Generates a new random base32 secret with the given amount of entropy in bits.

### `generateCode`

```php
public function generateCode(string $secret, ?int $time = null): string
```

Generates the one time password for a secret, for the current time or for the given timestamp.

### `generateQrData`

```php
public function generateQrData(string $secret, string $label): string
```

Builds an `otpauth://` URL for the secret, ready to be rendered as a QR code. The `$label` identifies the account inside the authenticator app.

### `verifyCode`

```php
public function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
```

Checks a submitted code against a window of time steps around now, to tolerate clock drift. `$discrepancy` sets how many steps before and after now are accepted.

## Exceptions

Beyond the constructor's `InvalidArgumentException`, the runtime methods throw exceptions implementing `TwoFactorAuthExceptionInterface` from [raxos/contract](/contract/):

- `TwoFactorAuthRandomizerException` from `createSecret` when the system's CSPRNG fails.
- `TwoFactorAuthInvalidDataException` from `generateCode` and `verifyCode` when the secret is not valid base32, raised while decoding it.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\TwoFactor\TwoFactorAuth;

$totp = new TwoFactorAuth(issuer: 'Passly');
$secret = $totp->createSecret();
$qrData = $totp->generateQrData($secret, 'user@example.com');

$isValid = $totp->verifyCode($secret, $submittedCode);
```
