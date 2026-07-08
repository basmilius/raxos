---
outline: deep
---

# Util classes

Foundation ships a set of `final` static helper classes in the `Raxos\Foundation\Util` namespace. Each groups a family of pure functions that are used throughout the Raxos ecosystem. This page tours the most used methods; the dedicated API references cover [ArrayUtil](/foundation/api/ArrayUtil), [StringUtil](/foundation/api/StringUtil) and [ColorUtil](/foundation/api/ColorUtil) in full.

## ArrayUtil

Helpers for working with plain arrays and iterables.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\ArrayUtil;

ArrayUtil::ensureArray($iterable);              // normalise to a plain array
ArrayUtil::flatten([[1, 2], [3, [4]]]);         // [1, 2, 3, 4]
ArrayUtil::groupBy($rows, 'type');              // group a list of arrays by a key
ArrayUtil::in($haystack, ['a', 'b'], all: true); // all items present?
ArrayUtil::only($row, ['id', 'name']);          // keep only these keys
ArrayUtil::first($items, static fn($item): bool => $item->active);
ArrayUtil::last($items);
```

## ColorUtil

Convert and blend colors between hex, RGB, RGBA, HSL and integer representations.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\ColorUtil;

ColorUtil::hexToRgb('#3366ff');            // [51, 102, 255]
ColorUtil::hexToRgba('#3366ffcc');         // [51, 102, 255, 0.8]
ColorUtil::rgbToHex(51, 102, 255, includeHashtag: true); // '#3366ff'
ColorUtil::rgbToHsl(51, 102, 255);         // [h, s, l]
ColorUtil::hslToRgb(220.0, 1.0, 0.6);      // [r, g, b]

ColorUtil::blend([255, 0, 0, 1], [0, 0, 255, 1], 50);
ColorUtil::shade([51, 102, 255, 1], 20);   // blend towards black
ColorUtil::tint([51, 102, 255, 1], 20);    // blend towards white

ColorUtil::luminance(51, 102, 255);
ColorUtil::lightOrDark([51, 102, 255]);    // a contrasting color
```

## MathUtil

Numeric helpers for clamping, stepping and fractions.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\MathUtil;

MathUtil::clamp(120, 0, 100);           // 100
MathUtil::ceilStep(23, 5);              // 25.0
MathUtil::floorStep(23, 5);             // 20.0
MathUtil::roundStep(23, 5);             // 25.0
MathUtil::greatestCommonDivisor(12, 8); // 4
MathUtil::simplifyFraction(6, 8);       // [3, 4]
```

## StringUtil

Common string manipulation tasks.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\StringUtil;

StringUtil::slugify('Hello, World!');        // 'hello-world'
StringUtil::toPascalCase('my_value');        // 'MyValue'
StringUtil::toSnakeCase('MyValue');          // 'my_value'
StringUtil::truncateText($longText, 20);
StringUtil::formatBytes(1_500_000);          // '1.5 MB'
StringUtil::random(12, dashes: true);
StringUtil::shortClassName(SomeClass::class); // 'SomeClass'
StringUtil::commaCommaAnd(['a', 'b', 'c']);  // 'a, b and c'
```

Other members include `splitSentences()`, `isSerialized()` and `multiByteSubstringReplace()`.

## XmlUtil

`XmlUtil::arrayToXml()` converts a nested array into a `SimpleXMLElement` tree, writing into the element passed by reference.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\XmlUtil;
use SimpleXMLElement;

$xml = new SimpleXMLElement('<root/>');

XmlUtil::arrayToXml([
    'name' => 'Raxos',
    'tags' => ['php', 'library']
], $xml);
```

## ReflectionUtil

Helpers to inspect reflection types and function parameters.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\ReflectionUtil;
use ReflectionMethod;

$parameters = ReflectionUtil::getParameters(new ReflectionMethod($object, 'handle'));
$types = ReflectionUtil::getTypes($parameter->getType());
```

## Debug

Static shortcuts for printing data during development. Each print method has a `Die` variant that prints and then halts execution.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\Debug;

Debug::dump($value);        // var_dump style
Debug::print($value);       // print_r style
Debug::json($value);        // JSON encoded

Debug::dumpDie($value);     // print and stop
Debug::ramUsage();          // current memory usage
```

## FileSystemUtil

Wrappers around temporary file creation that throw `TemporaryFileFailedException` on failure.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\FileSystemUtil;

$path = FileSystemUtil::temporaryFile();          // a temporary file path
$stream = FileSystemUtil::temporaryFileStream();  // an open temporary file handle
```
