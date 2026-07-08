---
outline: deep
---

# ColorUtil

`Raxos\Foundation\Util\ColorUtil` is a `final` class of static helper methods for converting and blending colors between hex, RGB, RGBA, HSL and integer representations.

See the [Util classes concept page](/foundation/utilities) for an overview of all utility classes.

## Signature

```php
namespace Raxos\Foundation\Util;

final class ColorUtil
```

## Conversion

```php
public static function hexToRgb(string $hex): array
```
Converts a hex color string to an `[r, g, b]` array.

```php
public static function hexToRgba(string $hex): array
```
Converts a hex color string to an `[r, g, b, a]` array.

```php
public static function rgbToHex(int $r, int $g, int $b, bool $includeHashtag = false): string
```
Converts RGB values to a hex string.

```php
public static function rgbaToHex(int $r, int $g, int $b, float $a, bool $includeHashtag = false): string
```
Converts RGBA values to a hex string.

```php
public static function rgbToHsl(int $r, int $g, int $b): array
```
Converts RGB values to an `[h, s, l]` array.

```php
public static function hslToRgb(float $h, float $s, float $l): array
```
Converts HSL values to an `[r, g, b]` array.

```php
public static function rgbToInt(int $r, int $g, int $b): int
public static function intToRgb(int $color): array
public static function intToRgba(int $color): array
```
Convert between RGB and packed integer representations.

## Blending and contrast

```php
public static function blend(array $color1, array $color2, int $weight = 0): array
```
Blends two RGBA colors together by the given weight.

```php
public static function shade(array $color, int $weight = 0): array
```
Blends the color with black by the given weight.

```php
public static function tint(array $color, int $weight = 0): array
```
Blends the color with white by the given weight.

```php
public static function luminance(int $r, int $g, int $b): float
```
Calculates the relative luminance of an RGB color.

```php
public static function yiq(int $r, int $g, int $b): float
```
Calculates the YIQ value of an RGB color.

```php
public static function lightOrDark(array $color, array $dark = [0, 0, 0], array $light = [255, 255, 255], float $delta = 0.5): array
```
Returns a light or dark contrasting color depending on the luminance of the input color.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\ColorUtil;

$rgb = ColorUtil::hexToRgb('#3366ff');   // [51, 102, 255]
$hex = ColorUtil::rgbToHex(51, 102, 255, includeHashtag: true); // '#3366ff'

$contrast = ColorUtil::lightOrDark([51, 102, 255]);
```
