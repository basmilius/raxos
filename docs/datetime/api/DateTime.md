---
outline: deep
---

# DateTime

`Raxos\DateTime\DateTime` is an immutable datetime value object based on CakePHP Chronos, with JSON
serialization and route parameter parsing support.

```php
class DateTime extends Chronos implements JsonSerializable, Stringable, StringParsableInterface
```

Because it extends `Cake\Chronos\Chronos`, every Chronos method (adding and subtracting intervals,
diffing, timezone handling, formatting and comparisons) is available.

## Methods

### jsonSerialize()

```php
public function jsonSerialize(): string
```

Returns the datetime formatted as an ISO 8601 string via `toIso8601String()`.

### __toString()

```php
public function __toString(): string
```

Returns the datetime formatted as `toDateTimeString()`.

### fromString()

```php
public static function fromString(string $input): static
```

Parses the given string into a `DateTime` instance through the inherited `parse()` method.

### pattern()

```php
public static function pattern(): string
```

Returns the regex pattern `\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}` for a date and time in the form
year, month, day, `T`, hour, minute, second, used to recognize a datetime in text, for example by
[raxos/router](/router/).

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\DateTime;

$moment = DateTime::fromString('2026-07-08T14:30:00');

echo $moment->addHours(2)->toDateTimeString(); // "2026-07-08 16:30:00"
echo json_encode($moment);                     // ISO 8601 string
```
