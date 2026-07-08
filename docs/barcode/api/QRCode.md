---
outline: deep
---

# QRCode

`Raxos\Barcode\QRCode` encodes data as a QR code with a configurable error correction level. It
extends [`Barcode`](/barcode/api/Barcode) and inherits its properties and render methods.

## Signature

```php
final readonly class QRCode extends Barcode
```

## Methods

### `__construct`

```php
__construct(
    string $data,
    QRCodeErrorCorrectionLevel $errorCorrectionLevel = QRCodeErrorCorrectionLevel::M
)
```

Builds a QR code matrix for `$data`. The optional `$errorCorrectionLevel` is a
`Raxos\Barcode\Enum\QRCodeErrorCorrectionLevel` case (`L`, `M`, `Q` or `H`) and defaults to `M`. A
higher level adds redundancy so the code stays scannable when partly damaged, at the cost of a
denser matrix.

Internally the class uses `QRCodeEncoder` and
[chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) to build the matrix.

## Inherited members

From [`Barcode`](/barcode/api/Barcode): the `data`, `format`, `height`, `width` and `matrix`
properties, and the `renderPng` and `renderSvg` methods. The `format` property is always
`BarcodeFormat::QR`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\QRCode;
use Raxos\Barcode\Enum\QRCodeErrorCorrectionLevel;

$qr = new QRCode('https://bas.dev', QRCodeErrorCorrectionLevel::Q);

$png = $qr->renderPng(scale: 6, margin: 12);

header('Content-Type: image/png');
echo $png;
```
