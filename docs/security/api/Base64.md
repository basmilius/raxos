---
outline: deep
---

# Base64

`Raxos\Security\Base64` is a static helper for base64 encoding and decoding, with standard, URL safe and shuffled variants.

```php
final class Base64
```

## Methods

### `encode`

```php
public static function encode(string $data): string
```

Encodes a string to standard base64.

### `decode`

```php
public static function decode(string $data): string
```

Decodes a standard base64 string. Throws `InvalidArgumentException` when the input is not valid base64.

### `encodeUrlSafe`

```php
public static function encodeUrlSafe(string $data): string
```

Encodes a string to base64 using URL safe characters, replacing `+` with `-` and `/` with `_`, and dropping the `=` padding.

### `decodeUrlSafe`

```php
public static function decodeUrlSafe(string $data): string
```

Decodes a URL safe base64 string back to its original value.

### `encodeShuffle`

```php
public static function encodeShuffle(string $data, int $amount = 1): string
```

Encodes to base64 and shifts each character code up by `$amount`. This is a light obfuscation step, not encryption.

### `decodeShuffle`

```php
public static function decodeShuffle(string $data, int $amount = 1): string
```

Reverses the character shift by `$amount` and decodes the resulting base64 string.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\Base64;

$token = Base64::encodeUrlSafe(random_bytes(16));
$bytes = Base64::decodeUrlSafe($token);
```
