---
outline: deep
---

# PNGRenderer

`Raxos\Barcode\Renderer\PNGRenderer` renders a barcode matrix to PNG image bytes using the `gd`
extension. It is the renderer behind [`Barcode::renderPng`](/barcode/api/Barcode), and can be used
directly when you want to reuse a configuration or read its `mimeType`.

## Signature

```php
final readonly class PNGRenderer extends GDRenderer implements RendererInterface
```

It extends the abstract `GDRenderer` (which builds the GD image from the matrix) and implements
`Raxos\Contract\Barcode\RendererInterface`.

## Properties

- `int $scale`: the size in pixels of a single module.
- `int $margin`: the quiet zone around the barcode in pixels.
- `string $backgroundColor`: the background hex color.
- `string $foregroundColor`: the dark module hex color.
- `string $mimeType`: always `image/png`.

## Methods

### `__construct`

```php
__construct(
    int $scale = 8,
    int $margin = 16,
    string $backgroundColor = '#ffffff',
    string $foregroundColor = '#000000'
)
```

Configures the rendering options and sets `mimeType` to `image/png`.

### `render`

```php
render(BarcodeInterface $barcode): string
```

Draws the matrix onto a GD true color image and returns the raw PNG bytes. Throws a
`RuntimeException` if the image cannot be created or the PNG output cannot be captured.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\QRCode;
use Raxos\Barcode\Renderer\PNGRenderer;

$barcode = new QRCode('https://bas.dev');
$renderer = new PNGRenderer(scale: 6, margin: 12);

$png = $renderer->render($barcode);

header('Content-Type: ' . $renderer->mimeType);
echo $png;
```

::: warning
This renderer requires the `gd` extension. When it is unavailable, use
[`SVGRenderer`](/barcode/api/SVGRenderer) instead.
:::
