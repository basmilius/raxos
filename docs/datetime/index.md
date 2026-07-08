---
outline: deep

cards:
    highlights:
        -   title: DateTime
            code: true
            details: 'Immutable datetime value object built on CakePHP Chronos.'
            link: /datetime/api/DateTime
        -   title: Date
            code: true
            details: 'Immutable date value object with JSON and route binding support.'
            link: /datetime/api/Date
        -   title: Time
            code: true
            details: 'Immutable time of day value object.'
            link: /datetime/api/Time
        -   title: DateTimeUtil
            code: true
            details: 'Small static helpers for time and ISO week calculations.'
            link: /datetime/api/DateTimeUtil
---

# DateTime

Date, time and datetime primitives for Raxos, built on top of CakePHP Chronos. The package ships
three small, immutable value classes (`Date`, `Time` and `DateTime`) that extend Chronos with JSON
serialization, string parsing and a regex pattern contract. It also provides `Month` and `Weekday`
enums, a small `DateTimeUtil` helper, and optional ORM casters for [raxos/database](/database/).

Install it with Composer.

```shell
composer require raxos/datetime
```

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Date, Time and DateTime](/datetime/value-objects): the three core value objects, how they
  extend Chronos, and how string parsing and JSON serialization work.
- [Enums and utilities](/datetime/enums-and-utilities): the `Month` and `Weekday` enums and the
  `DateTimeUtil` helper functions.
- [ORM casters](/datetime/orm-casters): the optional casters for [raxos/database](/database/)
  model properties.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\DateTime\{DateTime, Weekday};

$now = DateTime::now();
$weekday = Weekday::fromChronos($now);

echo $now->toIso8601String();
echo $weekday->name;
```

This creates the current `DateTime` and resolves the matching `Weekday` case.

## Next steps

See [installation](/datetime/installation) for requirements, or use the sidebar to navigate this
package.
