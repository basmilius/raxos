---
outline: deep
---

# Creating barcodes

Every barcode in this package is a `Barcode` instance. `Barcode` is an abstract `readonly` class
that takes your data, runs it through an encoder and stores the result as a boolean matrix. Two
concrete formats extend it: `QRCode` and `PDF417`. You never construct `Barcode` directly, you pick
one of the two formats.

## The Barcode base class

`Raxos\Barcode\Barcode` implements `Raxos\Contract\Barcode\BarcodeInterface`. Its constructor takes
the raw data, a `BarcodeFormat` and an `EncoderInterface`, then immediately encodes the data into
the `matrix` property. From the matrix it derives `height` (the number of rows) and `width` (the
number of columns).

Every barcode therefore exposes the same five read only properties:

- `data`: the original string that was encoded.
- `format`: a `BarcodeFormat` enum case identifying the format.
- `height`: the number of rows in the matrix.
- `width`: the number of columns in the matrix.
- `matrix`: a two dimensional array of booleans, where `true` is a dark module.

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\QRCode;

$barcode = new QRCode('https://bas.dev');

echo $barcode->format->value; // qr
echo $barcode->width;         // number of columns
echo $barcode->height;        // number of rows
```

## QR codes

`Raxos\Barcode\QRCode` encodes data as a QR code. Its constructor takes the data and an optional
error correction level, a `QRCodeErrorCorrectionLevel` case that defaults to `M`.

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\QRCode;
use Raxos\Barcode\Enum\QRCodeErrorCorrectionLevel;

$default = new QRCode('RAXOS');
$robust = new QRCode('RAXOS', QRCodeErrorCorrectionLevel::H);
```

The error correction level controls how much of the code can be damaged and still be scanned. The
four levels are `L` (lowest), `M`, `Q` and `H` (highest). A higher level adds redundancy at the
cost of a denser matrix. Under the hood, `QRCode` uses `QRCodeEncoder` and
[chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) to build the matrix.

## PDF417 barcodes

`Raxos\Barcode\PDF417` encodes data as a PDF417 barcode. Its constructor takes the data, an optional
column count (default `4`) and an optional security level (default `2`).

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\PDF417;

$default = new PDF417('RAXOS-0001-EXAMPLE');
$wider = new PDF417('RAXOS-0001-EXAMPLE', columns: 6, securityLevel: 3);
```

The column count sets how many data columns the symbol uses, which affects its width and aspect
ratio. The security level (0 to 8) controls the amount of error correction. PDF417 encoding is
handled by the package's own `PDF417Encoder`, no external library is involved.

## The BarcodeFormat enum

`Raxos\Barcode\Enum\BarcodeFormat` is a string backed enum with two cases, `QR` (`'qr'`) and
`PDF417` (`'pdf417'`). Every barcode exposes its format through the `$format` property, which is
handy when you need to branch on the format or persist it.

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\{PDF417, QRCode};
use Raxos\Barcode\Enum\BarcodeFormat;

$barcode = new PDF417('RAXOS');

if ($barcode->format === BarcodeFormat::PDF417) {
    // Handle the PDF417 case.
}
```

Once a barcode is built, see [Rendering to PNG or SVG](/barcode/rendering) to turn the matrix into
an image.
