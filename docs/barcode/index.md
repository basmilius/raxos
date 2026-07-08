---
outline: deep

cards:
    highlights:
        -   title: Barcode
            code: true
            details: 'Shared base class that encodes data into a matrix and renders PNG or SVG.'
            link: /barcode/api/Barcode
        -   title: QRCode
            code: true
            details: 'Encode data as a QR code with a configurable error correction level.'
            link: /barcode/api/QRCode
        -   title: PDF417
            code: true
            details: 'Encode data as a PDF417 barcode with configurable columns and security level.'
            link: /barcode/api/PDF417
        -   title: PNGRenderer
            code: true
            details: 'Render a barcode matrix to PNG image bytes using the GD extension.'
            link: /barcode/api/PNGRenderer
---

# Barcode

Generate QR codes and PDF417 barcodes and render them to PNG or SVG. Raxos Barcode is a small
library that wraps [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) for QR
encoding and ships its own PDF417 encoder. Both formats sit behind a single `Barcode` base class
that exposes a consistent matrix plus its height and width, along with `renderPng` and `renderSvg`
helpers.

Install it with Composer.

```shell
composer require raxos/barcode
```

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Creating barcodes](/barcode/creating-barcodes): the `Barcode` base class and the two concrete
  formats, `QRCode` and `PDF417`, including their construction options and the `BarcodeFormat`
  enum.
- [Rendering to PNG or SVG](/barcode/rendering): how to turn an encoded barcode into an image with
  the `renderPng` and `renderSvg` helpers, and the underlying `PNGRenderer` and `SVGRenderer`
  classes.

## Quick example

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

This creates a QR code with a higher error correction level and renders it straight to PNG bytes.

## Next steps

See [installation](/barcode/installation) for requirements, or use the sidebar to navigate this
package.
