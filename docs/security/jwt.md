---
outline: deep
---

# JSON Web Tokens

The `Raxos\Security\Jwt` namespace provides a JSON Web Token encoder and decoder. [`Jwt`](/security/api/Jwt) signs and verifies compact tokens, and [`JwtAlgorithm`](/security/api/JwtAlgorithm) selects the signing algorithm.

## Signing a token

`Jwt::encode` signs a payload array into a compact token string. The default algorithm is `HS256`.

```php
<?php
declare(strict_types=1);

use Raxos\Security\Jwt\{Jwt, JwtAlgorithm};

$token = Jwt::encode([
    'sub' => '1234',
    'iat' => time(),
    'exp' => time() + 3600,
], 'a-shared-secret', JwtAlgorithm::HS256);
```

You can attach a key id and extra header fields through the optional `keyId` and `headers` arguments. The encoder always sets the `typ` and `alg` headers itself.

## Verifying a token

`Jwt::decode` verifies the signature and validates the `nbf`, `iat` and `exp` claims before returning the payload. Pass the candidate keys as the second argument and, optionally, restrict the accepted algorithms with the third.

```php
$payload = Jwt::decode($token, ['a-shared-secret'], [JwtAlgorithm::HS256]);
```

When more than one key is supplied, the decoder selects the key by the `kid` header. The header must be present and must match a key in the array, otherwise decoding fails.

```php
$keys = [
    'key-2024' => 'secret-a',
    'key-2025' => 'secret-b',
];

$token = Jwt::encode($claims, 'secret-b', JwtAlgorithm::HS256, keyId: 'key-2025');
$payload = Jwt::decode($token, $keys);
```

## Algorithms

`JwtAlgorithm` supports HMAC and RSA families:

- HMAC: `HS256`, `HS384`, `HS512`. These use a shared secret for both signing and verifying.
- RSA: `RS256`, `RS384`, `RS512`. These sign with a private key and verify with the matching public key, using `ext-openssl`.

```php
$token = Jwt::encode($claims, $privateKeyPem, JwtAlgorithm::RS256);
$payload = Jwt::decode($token, [$publicKeyPem], [JwtAlgorithm::RS256]);
```

## Clock tolerance and testing

Two static properties on `Jwt` control time handling:

- `Jwt::$leeway` adds a tolerance, in seconds, when evaluating the `nbf`, `iat` and `exp` claims, to account for small clock differences between systems.
- `Jwt::$currentTime` overrides the current time used during decoding, which is useful for deterministic tests of time based claims.

```php
Jwt::$leeway = 60;
Jwt::$currentTime = 1704067200;
```

## Error handling

Decoding failures throw specific exceptions, all implementing `JwtExceptionInterface` from [raxos/contract](/contract/):

- `JwtExpiredException` when the `exp` claim is in the past.
- `JwtNotYetValidException` when the `nbf` or `iat` claim is in the future.
- `JwtInvalidSignatureException` when the signature does not match.
- `JwtUnsupportedException` when the token names an algorithm the package does not support.
- `JwtEncodingException` when the header or payload JSON cannot be encoded or decoded.
- `JwtEncryptionException` when the underlying RSA sign or verify operation fails, for example with a malformed key.
- `JwtNullException` when JSON encoding or decoding produces an unexpected null.

The last three also implement `JwtExceptionInterface` from [raxos/contract](/contract/), just like the ones already listed, so a single `catch (JwtExceptionInterface $err)` covers every JWT failure.

An `InvalidArgumentException` is thrown for malformed input, such as a token that does not have three segments or an empty key list.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Security\JwtExceptionInterface;
use Raxos\Security\Jwt\Jwt;

try {
    $payload = Jwt::decode($token, ['a-shared-secret']);
} catch (JwtExceptionInterface $err) {
    // The token is expired, not yet valid, tampered with or unsupported.
}
```
