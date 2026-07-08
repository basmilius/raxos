---
outline: deep

cards:
    highlights:
        -   title: Jwt
            code: true
            details: 'Encode and decode JSON Web Tokens with signature and claim validation.'
            link: /security/api/Jwt
        -   title: TwoFactorAuth
            code: true
            details: 'TOTP based two factor authentication with QR provisioning URLs.'
            link: /security/api/TwoFactorAuth
        -   title: Ulid
            code: true
            details: 'Lexicographically sortable identifiers that encode a millisecond timestamp.'
            link: /security/api/Ulid
        -   title: Hmac
            code: true
            details: 'Sign data with a shared secret and verify it in constant time.'
            link: /security/api/Hmac
---

# Security

Raxos Security is a compact collection of cryptographic primitives and identifier utilities used across Raxos applications. It provides base64 encoding variants, HMAC signing, cryptographically secure token generation, timing attack mitigation, short and sortable unique identifier generators, a JSON Web Token encoder and decoder with claim validation, and a TOTP based two factor authentication implementation with QR provisioning support.

Every class is a small, dependency light static helper or value object, built on PHP's openssl and hash extensions and the shared Raxos error and foundation packages.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Encoding, signing and tokens](/security/utilities): base64 variants, HMAC signatures, secure tokens and timing attack mitigation.
- [Identifiers](/security/identifiers): the NanoId and Ulid identifier generators.
- [JSON Web Tokens](/security/jwt): sign and verify JWTs with claim validation.
- [Two factor authentication](/security/two-factor-auth): the TOTP enrollment and login flow.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Security\Jwt\{Jwt, JwtAlgorithm};

$token = Jwt::encode([
    'sub' => '1234',
    'iat' => time(),
    'exp' => time() + 3600,
], 'a-shared-secret', JwtAlgorithm::HS256);

$payload = Jwt::decode($token, ['a-shared-secret'], [JwtAlgorithm::HS256]);
```

## Installation

Install it with Composer.

```shell
composer require raxos/security
```

See [installation](/security/installation) for requirements, or use the sidebar to navigate this package.
