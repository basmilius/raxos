---
outline: deep
---

# TokenGenerator

`Raxos\Security\TokenGenerator` generates cryptographically secure random tokens.

```php
final class TokenGenerator
```

## Methods

### `generateCryptographicallySecureToken`

```php
public static function generateCryptographicallySecureToken(int $length, bool $urlSafe = false): string
```

Generates a random token from `$length` bytes of `random_bytes` and encodes it as base64. When `$urlSafe` is `true`, the token is encoded as URL safe base64 instead.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\TokenGenerator;

$token = TokenGenerator::generateCryptographicallySecureToken(32, urlSafe: true);
```
