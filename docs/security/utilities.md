---
outline: deep
---

# Encoding, signing and tokens

This page covers the general purpose security helpers in the package root namespace: `Base64` encoding variants, `Hmac` signing, cryptographically secure token generation with `TokenGenerator`, and timing attack mitigation with `TimingAttackPrevention`. Each is a small static helper or value object with no configuration to speak of.

## Base64 encoding

[`Base64`](/security/api/Base64) offers three encoding styles: standard, URL safe and shuffled. Use the URL safe variant whenever the encoded value ends up in a URL, cookie or filename, since it replaces the `+` and `/` characters and drops the `=` padding.

```php
<?php
declare(strict_types=1);

use Raxos\Security\Base64;

$standard = Base64::encode('hello world');
$original = Base64::decode($standard);

$urlSafe = Base64::encodeUrlSafe('hello world');
$decoded = Base64::decodeUrlSafe($urlSafe);
```

`decode` throws an `InvalidArgumentException` when it receives input that is not valid base64.

The shuffled variant applies a simple character shift on top of standard base64. It is a light obfuscation step, not encryption, and both sides must agree on the shift amount.

```php
$obfuscated = Base64::encodeShuffle('hello world', 3);
$plain = Base64::decodeShuffle($obfuscated, 3);
```

## HMAC signatures

[`Hmac`](/security/api/Hmac) signs data with a shared secret and returns the signature as a URL safe base64 string. Verification uses `hash_equals`, so the comparison runs in constant time and does not leak information through timing.

```php
<?php
declare(strict_types=1);

use Raxos\Security\Hmac;

$signature = Hmac::get('user=1234', 'a-shared-secret');

$isValid = Hmac::matches($signature, 'user=1234', 'a-shared-secret');
```

The default algorithm is `sha256`. A different hashing algorithm can be passed as the third argument, for example `sha512`.

## Secure tokens

[`TokenGenerator`](/security/api/TokenGenerator) wraps `random_bytes` and base64 encodes the result. Pass the number of random bytes and, optionally, request URL safe output.

```php
<?php
declare(strict_types=1);

use Raxos\Security\TokenGenerator;

$token = TokenGenerator::generateCryptographicallySecureToken(32, urlSafe: true);
```

## Timing attack mitigation

[`TimingAttackPrevention`](/security/api/TimingAttackPrevention) pads an operation to a fixed minimum duration. It is useful around sensitive checks such as login or token verification, where a fast rejection and a slow rejection would otherwise reveal information to an attacker. Internally it uses the `Stopwatch` utility from [raxos/foundation](/foundation/).

```php
<?php
declare(strict_types=1);

use Raxos\Security\TimingAttackPrevention;

$timing = new TimingAttackPrevention(250);
$timing->begin();

// Perform the sensitive check here, for example verifying a password.

$timing->end();
```

Call `begin` before the sensitive work and `end` after it. If the work finished faster than the configured minimum (250 milliseconds in this example), `end` sleeps for the remaining time.
