---
outline: deep
---

# PKPass

`Raxos\Wallet\Apple\PKPass` is the main entry point that turns an [Identity](/wallet/api/Identity) and a [Pass](/wallet/api/Pass) into a signed `.pkpass` archive.

```php
final class PKPass
```

## Constructor

```php
public function __construct(
    public readonly Identity $identity,
    public readonly Pass $pass
)
```

On construction, `PKPass` opens an internal `Archive` and writes `pass.json`, merging the identity's `passTypeIdentifier` and `teamIdentifier` into the pass data. It can throw a `JsonException` while encoding.

## Properties

### `fileName: string`

A computed read only property returning `{serialNumber}.pkpass` from the pass. Used as the download name in `respond()`.

## Methods

### `file(string $fileName, string $localFileName): void`

Adds an asset from disk under `$fileName` and records its SHA1 hash in the manifest.

### `fileContents(string $fileName, string $contents): void`

Adds an asset from a string under `$fileName` and records its SHA1 hash in the manifest.

### `strings(Strings $strings): void`

Writes a [Strings](/wallet/api/Strings) instance to `<language>.lproj/pass.strings` inside the archive.

### `sign(): void`

Builds `manifest.json` from the recorded hashes, signs it against the bundled WWDR certificate with the identity credentials using `openssl_pkcs7_sign`, and stores `manifest.json` and the detached `signature` in the archive. Throws a `RuntimeException` if signing fails, and can throw `JsonException` or `TemporaryFileFailedException`.

### `binary(): string`

Returns the raw bytes of the archive. Delegates to `Archive::binary()`.

### `close(): void`

Finalizes the zip. Delegates to `Archive::close()`.

### `delete(): void`

Removes the underlying temporary file. Delegates to `Archive::delete()`.

### `respond(): HttpResponse`

Returns an [HttpResponse](/http/) with the `application/vnd.apple.pkpass` content type and a `Content-Disposition: attachment` header using `fileName`. Delegates to `Archive::respond()`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\{Identity, PKPass};
use Raxos\Wallet\Apple\Component\Pass;

$pkpass = new PKPass($identity, $pass);
$pkpass->file('icon.png', __DIR__ . '/assets/icon.png');
$pkpass->sign();
$pkpass->close();

$response = $pkpass->respond();
$response->send();

$pkpass->delete();
```

## See also

- [Signing and packaging](/wallet/signing-and-packaging)
- [PKPassBundle](/wallet/api/PKPassBundle)
- [Identity](/wallet/api/Identity)
