---
outline: deep
---

# SVGRenderer

`Raxos\Barcode\Renderer\SVGRenderer` renders a barcode matrix to an SVG document. It is the renderer
behind [`Barcode::renderSvg`](/barcode/api/Barcode), and can be used directly when you want to reuse
a configuration or read its `mimeType`. Unlike the PNG renderer, it needs no extra PHP extension.

## Signature

```php
final readonly class SVGRenderer extends Renderer implements RendererInterface
```

It extends the abstract `Renderer` base class and implements
`Raxos\Contract\Barcode\RendererInterface`.

## Properties

- `int $scale`: the size in pixels of a single module.
- `int $margin`: the quiet zone around the barcode in pixels.
- `string $backgroundColor`: the background hex color.
- `string $foregroundColor`: the dark module hex color.
- `string $mimeType`: always `image/svg+xml`.

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

Configures the rendering options and sets `mimeType` to `image/svg+xml`.

### `render`

```php
render(BarcodeInterface $barcode): string
```

Returns a full SVG document string. Horizontal runs of adjacent dark modules are merged into single
`<rect>` elements to keep the markup compact.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Barcode\PDF417;
use Raxos\Barcode\Renderer\SVGRenderer;

$barcode = new PDF417('RAXOS-0001-EXAMPLE');
$renderer = new SVGRenderer(scale: 4, margin: 8);

$svg = $renderer->render($barcode);
```
