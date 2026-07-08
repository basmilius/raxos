---
outline: deep
---

# Date, Time and DateTime

The package centers on three immutable value objects. Each one extends a CakePHP Chronos class, so
every Chronos method (adding and subtracting intervals, diffing, formatting and comparing) is
available out of the box.

| Class      | Extends                    | Represents             |
|------------|----------------------------|------------------------|
| `Date`     | `Cake\Chronos\ChronosDate` | A calendar date        |
| `Time`     | `Cake\Chronos\ChronosTime` | A time of day          |
| `DateTime` | `Cake\Chronos\Chronos`     | A date with a time     |

## Creating instances

Because the classes extend Chronos, they are created the same way Chronos objects are: through
`parse()`, `now()`, `createFromFormat()` or the constructor.

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\{Date, DateTime, Time};

$date = Date::parse('2026-07-08');
$time = Time::parse('14:30:00');
$now = DateTime::now();

$tomorrow = $date->addDays(1);
$difference = $now->diffInHours($now->addHours(3));
```

## JSON serialization

Every value object implements `JsonSerializable` and serializes to an ISO style string, so encoding
a model or a response payload produces predictable output.

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\{Date, DateTime, Time};

echo json_encode([
    'date' => Date::parse('2026-07-08'),      // "2026-07-08"
    'time' => Time::parse('14:30:00'),        // "14:30:00"
    'moment' => DateTime::parse('2026-07-08 14:30:00'), // ISO 8601 string
]);
```

- `Date::jsonSerialize()` returns `toDateString()`.
- `Time::jsonSerialize()` returns the string cast of the instance.
- `DateTime::jsonSerialize()` returns `toIso8601String()`.

## String conversion

`Date` and `DateTime` implement `Stringable` explicitly. Casting them to a string returns a stable
representation.

```php
(string) Date::parse('2026-07-08');                 // "2026-07-08"
(string) DateTime::parse('2026-07-08 14:30:00');    // "2026-07-08 14:30:00"
```

`Date::__toString()` returns `toDateString()` and `DateTime::__toString()` returns
`toDateTimeString()`. `Time` inherits its string behavior from Chronos.

## String parsing and route binding

Each class implements `StringParsableInterface` from [raxos/foundation](/foundation/), which pairs
two static methods:

- `fromString(string $input): static` parses a string into an instance through the inherited
  `parse()` method.
- `pattern(): string` returns a regex fragment that recognizes the value in text.

```php
Date::pattern();     // '\d{4}-\d{2}-\d{2}'
Time::pattern();     // '\d{2}:\d{2}:\d{2}'
DateTime::pattern(); // '\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}'
```

[raxos/router](/router/) uses this contract to bind path parameters directly to a `Date`, `Time` or
`DateTime`. The router uses `pattern()` to match the segment and `fromString()` to build the
instance.

```php
#[Get('/events/{date}')]
public function forDate(Date $date): array
{
    // $date is parsed automatically from the path using Date::fromString().
}
```

## Reference pages

- [Date](/datetime/api/Date)
- [DateTime](/datetime/api/DateTime)
- [Time](/datetime/api/Time)
