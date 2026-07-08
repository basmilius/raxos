---
outline: deep
---

# Enums and utilities

Alongside the value objects, the package ships two backed enums and a small static helper class.

## Month

`Month` is an `int` backed enum with a case for every calendar month, from `JANUARY` (1) through
`DECEMBER` (12). The value matches the natural month number.

Its `fromChronos()` helper maps the month of a `Date` or `DateTime` to the matching case.

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\{DateTime, Month};

$month = Month::fromChronos(DateTime::parse('2026-07-08'));

echo $month->name;   // "JULY"
echo $month->value;  // 7
```

## Weekday

`Weekday` is an `int` backed enum numbered `SUNDAY` (0) through `SATURDAY` (6), matching PHP's zero
based day numbering.

Its `fromChronos()` helper reads the Chronos `dayOfWeek` value (ISO 1 to 7) and returns the matching
case, so the ISO numbering is translated to the enum's own numbering.

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\{Date, Weekday};

$weekday = Weekday::fromChronos(Date::parse('2026-07-08'));

echo $weekday->name;   // "WEDNESDAY"
echo $weekday->value;  // 3
```

## DateTimeUtil

`DateTimeUtil` is a `final` class with two static helpers.

### timeToSeconds()

Converts a colon separated time string to a total number of seconds. Missing parts default to zero,
so both `H:i:s` and shorter forms are accepted.

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\DateTimeUtil;

DateTimeUtil::timeToSeconds('01:30:00'); // 5400
DateTimeUtil::timeToSeconds('00:00:45'); // 45
```

### weekIdentifier()

Returns the ISO week identifier for a `Date` or `DateTime` in the `o\WW` format, an ISO year followed
by `W` and the two digit week number.

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\{Date, DateTimeUtil};

DateTimeUtil::weekIdentifier(Date::parse('2026-07-08')); // "2026W28"
```

See the [DateTimeUtil reference](/datetime/api/DateTimeUtil) for the full signatures.
