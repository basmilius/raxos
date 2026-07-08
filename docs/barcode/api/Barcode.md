---
outline: deep
---

# Barcode

`Raxos\Barcode\Barcode` is the abstract base class shared by every barcode format. It encodes the
given data into a boolean matrix through an `EncoderInterface` and offers PNG and SVG rendering. You
do not instantiate it directly, use [`QRCode`](/barcode/api/QRCode) or
[`PDF417`](/barcode/api/PDF417).

## Signature

```php
abstract readonly class Barcode implements BarcodeInterface
```

It implements `Raxos\Contract\Barcode\BarcodeInterface`.

## Properties

All properties are public and read only.

- `string $data`: the original data that was encoded.
- `BarcodeFormat $format`: the format of this barcode.
- `int $height`: the number of rows in the matrix.
- `int $width`: the number of columns in the matrix.
- `array $matrix`: the encoded data as a two dimensional array of booleans.

## Methods

### `__construct`

```php
__construct(string $data, BarcodeFormat $format, EncoderInterface $encoder)
```

Encodes `$data` with the given encoder and populates `matrix`, then derives `height` and `width`
from it. Called by the concrete subclasses.

### `renderPng`

```php
renderPng(
    int $scale = 8,
    int $margin = 16,
    string $backgroundColor = '#ffffff',
    string $foregroundColor = '#000000'
): string
```

Renders the barcode to PNG image bytes using a [`PNGRenderer`](/barcode/api/PNGRenderer). Requires
the `gd` extension.

### `renderSvg`

```php
renderSvg(
    int $scale = 8,
    int $margin = 16,
    string $backgroundColor = '#ffffff',
    string $foregroundColor = '#000000'
): string
```

Renders the barcode to an SVG document string using an [`SVGRenderer`](/barcode/api/SVGRenderer).

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\QRCode;

$barcode = new QRCode('https://bas.dev');

// Inherited from Barcode.
$png = $barcode->renderPng(scale: 6, margin: 12);
$svg = $barcode->renderSvg();

echo $barcode->width . 'x' . $barcode->height;
```

See [Creating barcodes](/barcode/creating-barcodes) for the concrete formats and
[Rendering to PNG or SVG](/barcode/rendering) for the render options.
