---
outline: deep
---

# PKPassBundle

`Raxos\Wallet\Apple\PKPassBundle` groups multiple signed [PKPass](/wallet/api/PKPass) instances into a single `.pkpasses` archive.

```php
final readonly class PKPassBundle
```

## Constructor

```php
public function __construct(
    public string $fileName
)
```

Opens an internal `Archive` for the bundle. `$fileName` is the download name used by `respond()`.

## Methods

### `add(PKPass $pass): void`

Appends an already signed `PKPass` by reading its `binary()` output into the archive under the pass file name. Sign and close the pass before adding it.

### `binary(): string`

Returns the raw bytes of the bundle archive.

### `close(): void`

Finalizes the zip.

### `delete(): void`

Removes the underlying temporary file.

### `respond(): HttpResponse`

Returns an [HttpResponse](/http/) with the `application/vnd.apple.pkpasses` content type and a `Content-Disposition: attachment` header using `fileName`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\PKPassBundle;

$bundle = new PKPassBundle('tickets.pkpasses');

foreach ($passes as $pkpass) {
    $pkpass->sign();
    $pkpass->close();

    $bundle->add($pkpass);
}

$bundle->close();

return $bundle->respond();
```

## See also

- [Bundles and localization](/wallet/bundles-and-localization)
- [PKPass](/wallet/api/PKPass)
