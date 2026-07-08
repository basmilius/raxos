---
outline: deep
---

# Rendering to PNG or SVG

An encoded barcode is just a matrix of booleans. To turn it into an image, use the two render
helpers on the `Barcode` base class, or use the renderer classes directly for more control.

## The render helpers

`Barcode::renderPng` and `Barcode::renderSvg` render the matrix to an image string. Both accept the
same four options:

- `scale`: the size in pixels of a single module (default `8`).
- `margin`: the quiet zone around the barcode in pixels (default `16`).
- `backgroundColor`: a hex color for the background (default `#ffffff`).
- `foregroundColor`: a hex color for the dark modules (default `#000000`).

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\QRCode;

$barcode = new QRCode('https://bas.dev');

$png = $barcode->renderPng(scale: 6, margin: 12);
$svg = $barcode->renderSvg(
    backgroundColor: '#ffffff',
    foregroundColor: '#111111'
);
```

`renderPng` returns raw PNG bytes, `renderSvg` returns a full SVG document as a string. Colors are
passed as hex strings such as `#ffffff` and converted internally with `ColorUtil` from
[raxos/foundation](/foundation/).

## The PNG renderer

`Raxos\Barcode\Renderer\PNGRenderer` draws the matrix onto a GD true color image and returns the
PNG bytes. It requires the `gd` extension. The `renderPng` helper is a thin wrapper around it, so
these two calls are equivalent:

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\QRCode;
use Raxos\Barcode\Renderer\PNGRenderer;

$barcode = new QRCode('https://bas.dev');

$viaHelper = $barcode->renderPng(scale: 6);
$viaRenderer = new PNGRenderer(scale: 6)->render($barcode);
```

Using the renderer directly is useful when you want to reuse the same configuration across several
barcodes, or when you want to read its `mimeType` property, which is set to `image/png`.

::: warning
The PNG renderer needs the `gd` extension. It throws a `RuntimeException` if the image cannot be
created or the PNG output cannot be captured.
:::

## The SVG renderer

`Raxos\Barcode\Renderer\SVGRenderer` builds an SVG document from the matrix. It has no extra
extension requirements, so it works even where GD is unavailable. To keep the output small, it
merges horizontal runs of adjacent dark modules into a single `<rect>` element rather than emitting
one rectangle per module.

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\PDF417;
use Raxos\Barcode\Renderer\SVGRenderer;

$barcode = new PDF417('RAXOS-0001-EXAMPLE');

$svg = new SVGRenderer(scale: 4, margin: 8)->render($barcode);
```

Its `mimeType` property is set to `image/svg+xml`.

## Choosing a format

Use PNG when you need a raster image to embed or serve as a file, for example behind an image
endpoint. Use SVG when you want a resolution independent, easily styled document, or when the `gd`
extension is not available in your environment.
