---
outline: deep
---

# Barcode

`Raxos\Wallet\Apple\Component\Barcode` is a barcode entry attached to a [Pass](/wallet/api/Pass), combining a format, a message and optional alt text.

```php
final readonly class Barcode implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

| Parameter         | Type            | Description                                             |
|-------------------|-----------------|---------------------------------------------------------|
| `format`          | `BarcodeFormat` | The barcode symbology.                                   |
| `message`         | `string`        | The encoded payload.                                    |
| `altText`         | `string\|null`  | Human readable text shown under the barcode.            |
| `messageEncoding` | `string`        | Encoding of the message, defaults to `iso-8859-1`.      |

## BarcodeFormat

The `Raxos\Wallet\Apple\Enum\BarcodeFormat` backed enum has four cases, each mapping to an Apple PassKit constant:

| Case      | Value                     |
|-----------|---------------------------|
| `AZTEC`   | `PKBarcodeFormatAztec`    |
| `CODE128` | `PKBarcodeFormatCode128`  |
| `PDF417`  | `PKBarcodeFormatPDF417`   |
| `QR`      | `PKBarcodeFormatQR`       |

## Methods

### `jsonSerialize(): array`

Returns the barcode data, filtering out null and empty values.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\Barcode;
use Raxos\Wallet\Apple\Enum\BarcodeFormat;

$barcode = new Barcode(
    format: BarcodeFormat::PDF417,
    message: 'TCK-00042',
    altText: 'TCK-00042'
);
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
