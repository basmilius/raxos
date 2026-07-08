---
outline: deep
---

# JwtAlgorithm

`Raxos\Security\Jwt\JwtAlgorithm` enumerates the signing algorithms supported by [`Jwt`](/security/api/Jwt).

```php
enum JwtAlgorithm: string
```

## Cases

| Case    | Family | Hash   |
|---------|--------|--------|
| `HS256` | HMAC   | sha256 |
| `HS384` | HMAC   | sha384 |
| `HS512` | HMAC   | sha512 |
| `RS256` | RSA    | sha256 |
| `RS384` | RSA    | sha384 |
| `RS512` | RSA    | sha512 |

HMAC algorithms use a shared secret for both signing and verifying. RSA algorithms sign with a private key and verify with the matching public key, using `ext-openssl`.

## Methods

### `sign`

```php
public function sign(string $key, string $message): string
```

Signs a message with the given key using this algorithm, returning the raw signature.

### `verify`

```php
public function verify(string $key, string $signature, string $message): bool
```

Verifies a message and signature against the given key. HMAC verification uses `hash_equals` for a constant time comparison.

## Exceptions

Both methods are declared to throw `JwtExceptionInterface` from [raxos/contract](/contract/):

- `sign` throws `JwtEncryptionException` when the OpenSSL signing call fails for the RSA algorithms.
- `verify` throws `JwtEncryptionException` when OpenSSL reports an error during verification for the RSA algorithms.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\Jwt\{Jwt, JwtAlgorithm};

$token = Jwt::encode($claims, $privateKeyPem, JwtAlgorithm::RS256);
$payload = Jwt::decode($token, [$publicKeyPem], [JwtAlgorithm::RS256]);
```
