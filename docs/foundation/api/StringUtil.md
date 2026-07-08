---
outline: deep
---

# StringUtil

`Raxos\Foundation\Util\StringUtil` is a `final` class of static helper methods for common string manipulation tasks.

See the [Util classes concept page](/foundation/utilities) for an overview of all utility classes.

## Signature

```php
namespace Raxos\Foundation\Util;

final class StringUtil
```

## Methods

```php
public static function slugify(string $str): string
```
Transliterates and normalises a string into a URL friendly slug.

```php
public static function toPascalCase(string $str): string
```
Converts a string to PascalCase.

```php
public static function toSnakeCase(string $str): string
```
Converts a string to snake_case.

```php
public static function truncateText(string $text, int $wordCount = 20, string $ending = '...'): string
```
Strips tags and headings and truncates text to a maximum word count.

```php
public static function formatBytes(int $value, int $decimals = 2, bool $siMode = true, bool $bits = false): string
```
Formats a byte or bit count into a human readable string with SI or IEC suffixes.

```php
public static function random(int $length = 9, bool $dashes = false, string $sets = 'luds'): string
```
Generates a random string from lowercase, uppercase, digit and symbol character sets, optionally dash separated.

```php
public static function shortClassName(string $className): string
```
Returns the class name without its namespace.

```php
public static function commaCommaAnd(array $strings): string
```
Joins a list of strings with commas and a trailing `and`.

```php
public static function splitSentences(string $str): array
```
Splits a string into an array of sentences.

```php
public static function isSerialized(string $data): bool
```
Returns true if the string is a serialised PHP value.

```php
public static function multiByteSubstringReplace(string $str, string $replacement, int $start, ?int $length = null): string
```
A multibyte aware variant of `substr_replace`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\StringUtil;

$slug = StringUtil::slugify('Café del Mar'); // 'cafe-del-mar'
$size = StringUtil::formatBytes(1_500_000);  // '1.5 MB'
$name = StringUtil::shortClassName(StringUtil::class); // 'StringUtil'
```
