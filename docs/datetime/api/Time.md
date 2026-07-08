---
outline: deep
---

# Time

`Raxos\DateTime\Time` is an immutable time of day value object based on CakePHP Chronos, with JSON
serialization and route parameter parsing support.

```php
class Time extends ChronosTime implements JsonSerializable, Stringable, StringParsableInterface
```

Because it extends `Cake\Chronos\ChronosTime`, every Chronos time method is available.

## Methods

### jsonSerialize()

```php
public function jsonSerialize(): string
```

Returns the string representation of the time (the string cast of the instance).

### fromString()

```php
public static function fromString(string $input): static
```

Parses the given string into a `Time` instance through the inherited `parse()` method.

### pattern()

```php
public static function pattern(): string
```

Returns the regex pattern `\d{2}:\d{2}:\d{2}` (hour, minute, second) used to recognize a time in
text, for example by [raxos/router](/router/).

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\Time;

$time = Time::fromString('14:30:00');

echo (string) $time;      // "14:30:00"
echo json_encode($time);  // "14:30:00"
```
