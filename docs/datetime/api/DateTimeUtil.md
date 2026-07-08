---
outline: deep
---

# DateTimeUtil

`Raxos\DateTime\DateTimeUtil` is a small collection of static helper functions for time and week
calculations.

```php
final class DateTimeUtil
```

## Methods

### timeToSeconds()

```php
public static function timeToSeconds(string $time): int
```

Converts a colon separated time string into a total number of seconds. Missing parts default to
zero.

```php
DateTimeUtil::timeToSeconds('01:30:00'); // 5400
```

### weekIdentifier()

```php
public static function weekIdentifier(Date|DateTime $date): string
```

Returns the ISO week identifier for the given date in the `o\WW` format: an ISO year followed by
`W` and the two digit week number.

```php
DateTimeUtil::weekIdentifier(Date::parse('2026-07-08')); // "2026W28"
```

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\{Date, DateTimeUtil};

$seconds = DateTimeUtil::timeToSeconds('02:15:30');
$week = DateTimeUtil::weekIdentifier(Date::now());
```
