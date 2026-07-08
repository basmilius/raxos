---
outline: deep
---

# Signing and packaging

`Raxos\Wallet\Apple\PKPass` is the main entry point of the package. It turns an [Identity](/wallet/api/Identity) and a [Pass](/wallet/api/Pass) into a signed `.pkpass` archive: it writes `pass.json`, collects your assets, tracks a manifest, signs that manifest with OpenSSL, and hands you a ready to send response.

## Constructing a PKPass

Pass an `Identity` and a `Pass` to the constructor. On construction, `PKPass` opens an internal [archive](#the-archive) and writes `pass.json`, merging the issuer's `passTypeIdentifier` and `teamIdentifier` from the identity into the pass data.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\{Identity, PKPass};
use Raxos\Wallet\Apple\Component\Pass;

$identity = new Identity(
    certificate: file_get_contents(__DIR__ . '/certificate.pem'),
    privateKey: file_get_contents(__DIR__ . '/private-key.pem'),
    password: 'secret',
    passTypeIdentifier: 'pass.dev.example.card',
    teamIdentifier: 'ABCDE12345'
);

$pass = new Pass(
    description: 'Membership card',
    organizationName: 'Example Club',
    serialNumber: 'MBR-2026-0001'
);

$pkpass = new PKPass($identity, $pass);
```

The read only `fileName` property computes `{serialNumber}.pkpass` from the pass, which is used as the download name in `respond()`.

## Adding assets

An Apple Wallet pass needs images (at least `icon.png`), and often a logo and strip. Add them from disk with `file()` or from an in memory string with `fileContents()`. Both methods update the internal SHA1 manifest that gets signed.

```php
$pkpass->file('icon.png', __DIR__ . '/assets/icon.png');
$pkpass->file('logo.png', __DIR__ . '/assets/logo.png');
$pkpass->fileContents('thumbnail.png', $generatedThumbnail);
```

## Signing

`sign()` builds `manifest.json` from the SHA1 hashes of every added file, signs it against the bundled WWDR certificate with the identity's certificate, private key and password (using `openssl_pkcs7_sign`), and stores both `manifest.json` and the detached `signature` inside the archive.

```php
$pkpass->sign();
```

::: warning Add assets before signing
The manifest is built from the files added so far. Add every asset and every [localization](/wallet/bundles-and-localization) before you call `sign()`. Anything added afterwards will not be covered by the signature.
:::

If OpenSSL fails to produce a signature, `sign()` throws a `RuntimeException`. It can also throw a `JsonException` when encoding the manifest and a `TemporaryFileFailedException` when it cannot create the temporary files it needs.

## The archive

Under the hood, `PKPass` delegates to `Raxos\Wallet\Archive`, a thin `final readonly class` wrapping `ZipArchive`. The archive is backed by a temporary file created through `Raxos\Foundation\Util\FileSystemUtil` (see [raxos/foundation](/foundation/)). `PKPass` forwards the archive lifecycle methods:

- `binary()` returns the raw bytes of the archive.
- `close()` finalizes the zip so its bytes can be read.
- `delete()` removes the underlying temporary file.
- `respond()` returns an [HttpResponse](/http/) with the pass content type and a download filename.

## Sending and cleaning up

Call `close()` after `sign()` so the zip is flushed, then respond. Once the response has been sent, call `delete()` to remove the temporary file.

```php
$pkpass->sign();
$pkpass->close();

$response = $pkpass->respond();
$response->send();

$pkpass->delete();
```

`respond()` returns a binary `HttpResponse` (see [raxos/http](/http/)) carrying the `application/vnd.apple.pkpass` content type and a `Content-Disposition: attachment` header with the computed file name, so browsers and the Wallet app treat it as a downloadable pass.

Continue to [bundles and localization](/wallet/bundles-and-localization) to group multiple passes and add localized strings.
