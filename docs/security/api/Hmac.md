---
outline: deep
---

# Hmac

`Raxos\Security\Hmac` creates and verifies HMAC signatures, encoded as URL safe base64.

```php
final class Hmac
```

## Methods

### `get`

```php
public static function get(string $data, string $key, string $algo = 'sha256'): string
```

Returns the URL safe base64 encoded HMAC signature for the given data and key. The default hashing algorithm is `sha256`.

### `matches`

```php
public static function matches(string $actual, string $data, string $key, string $algo = 'sha256'): bool
```

Returns `true` when `$actual` matches the signature computed for the data and key. The comparison uses `hash_equals`, so it runs in constant time.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\Hmac;

$payload = 'user=1234';
$signature = Hmac::get($payload, 'a-shared-secret');

if (Hmac::matches($signature, $payload, 'a-shared-secret')) {
    // The payload has not been tampered with.
}
```
