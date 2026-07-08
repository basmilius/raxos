---
outline: deep
---

# Date

`Raxos\DateTime\Date` is an immutable date value object based on CakePHP Chronos, with JSON
serialization and route parameter parsing support.

```php
class Date extends ChronosDate implements JsonSerializable, Stringable, StringParsableInterface
```

Because it extends `Cake\Chronos\ChronosDate`, every Chronos date method (adding and subtracting
days, comparisons, formatting) is available.

## Methods

### jsonSerialize()

```php
public function jsonSerialize(): string
```

Returns the date formatted as `toDateString()`.

### __toString()

```php
public function __toString(): string
```

Returns the date formatted as `toDateString()`.

### fromString()

```php
public static function fromString(string $input): static
```

Parses the given string into a `Date` instance through the inherited `parse()` method.

### pattern()

```php
public static function pattern(): string
```

Returns the regex pattern `\d{4}-\d{2}-\d{2}` (four digit year, two digit month, two digit day) used
to recognize a date in text, for example by [raxos/router](/router/).

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\Date;

$date = Date::fromString('2026-07-08');

echo $date->addDays(7)->toDateString(); // "2026-07-15"
echo json_encode($date);                // "2026-07-08"
```
