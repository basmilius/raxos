---
outline: deep
---

# Color

`Raxos\Wallet\Component\Color` is an RGB color value object used for the `backgroundColor`, `foregroundColor`, `labelColor` and `footerBackgroundColor` of a [Pass](/wallet/api/Pass).

```php
final readonly class Color implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)). It uses `ColorUtil` from [raxos/foundation](/foundation/) for hex conversion.

## Constructor

| Parameter | Type  | Description               |
|-----------|-------|---------------------------|
| `red`     | `int` | Red channel, 0 to 255.    |
| `green`   | `int` | Green channel, 0 to 255.  |
| `blue`    | `int` | Blue channel, 0 to 255.   |

The `red`, `green` and `blue` properties are public.

## Methods

### `toHex(): string`

Returns the hex representation of the color, prefixed with `#`, via `ColorUtil::rgbToHex()`.

### `toRgb(): string`

Returns the `rgb(r, g, b)` string. This is also what `jsonSerialize()` emits, which is the format Apple expects.

### `jsonSerialize(): string`

Returns the `rgb()` string form of the color.

### `static fromHex(string $hex): self`

Named constructor that parses a hex string into a `Color`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Component\Color;

$blue = new Color(10, 132, 255);
$blue->toHex(); // "#0a84ff"
$blue->toRgb(); // "rgb(10, 132, 255)"

$fromHex = Color::fromHex('#1c1c1e');
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
- [Building a pass](/wallet/pass-structure)
