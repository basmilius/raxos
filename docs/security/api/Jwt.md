---
outline: deep
---

# Jwt

`Raxos\Security\Jwt\Jwt` encodes and decodes JSON Web Tokens, with claim validation for expiry and validity windows.

```php
final class Jwt
```

## Properties

### `$currentTime`

```php
public static ?int $currentTime = null;
```

Overrides the current time used during decoding. Useful for deterministic tests of time based claims. When `null`, the decoder uses `time()`.

### `$leeway`

```php
public static int $leeway = 0;
```

Tolerance in seconds applied when evaluating the `nbf`, `iat` and `exp` claims, to account for clock differences between systems.

## Methods

### `encode`

```php
public static function encode(
    array $payload,
    string $key,
    JwtAlgorithm $algorithm = JwtAlgorithm::HS256,
    ?string $keyId = null,
    array $headers = []
): string
```

Signs the given payload into a compact JWT string using the chosen algorithm. Optional `$keyId` sets the `kid` header, and `$headers` adds extra header fields.

### `decode`

```php
public static function decode(string $jwt, array $keys, array $allowedAlgorithms = []): array
```

Verifies the signature and the `nbf`, `iat` and `exp` claims, then returns the decoded payload. When more than one key is supplied, the key is selected by the `kid` header. Passing `$allowedAlgorithms` restricts which algorithms are accepted.

## Exceptions

Both `encode` and `decode` throw exceptions implementing `JwtExceptionInterface` from [raxos/contract](/contract/): `JwtExpiredException`, `JwtNotYetValidException`, `JwtInvalidSignatureException`, `JwtUnsupportedException`, `JwtEncodingException` (JSON encoding or decoding failed), `JwtEncryptionException` (surfaced from `JwtAlgorithm::sign` and `verify` for the RSA algorithms) and `JwtNullException` (JSON produced an unexpected null). Malformed input throws `InvalidArgumentException`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\Jwt\{Jwt, JwtAlgorithm};

$token = Jwt::encode([
    'sub' => '1234',
    'exp' => time() + 3600,
], 'a-shared-secret', JwtAlgorithm::HS256);

$payload = Jwt::decode($token, ['a-shared-secret'], [JwtAlgorithm::HS256]);
```
