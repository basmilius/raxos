---
outline: deep

cards:
    highlights:
        -   title: Option
            code: true
            details: 'A functional optional value type with Some and None, no more juggling null.'
            link: /foundation/api/Option
        -   title: IP
            code: true
            details: 'Validate and parse IPv4 and IPv6 addresses into a small value object.'
            link: /foundation/api/IP
        -   title: Singleton
            code: true
            details: 'A tiny registry that keeps one shared instance per class name.'
            link: /foundation/api/Singleton
        -   title: Stopwatch
            code: true
            details: 'Measure elapsed time with a high resolution timer and report it in any unit.'
            link: /foundation/api/Stopwatch
        -   title: ArrayUtil
            code: true
            details: 'Flatten, group, filter and slice plain arrays and iterables.'
            link: /foundation/api/ArrayUtil
        -   title: StringUtil
            code: true
            details: 'Slugify, case convert, truncate and format strings.'
            link: /foundation/api/StringUtil
---

# Foundation

Raxos Foundation is the small, dependency free package that underpins every other Raxos library. It has no dependencies on other Raxos packages and provides the primitives that keep the rest of the ecosystem consistent: magic property and array access traits, a functional Option type for representing optional values, an IP value object, a Singleton registry, and a set of static Util classes for arrays, colors, math, strings, XML, reflection, timing and debug output. A handful of global functions round out the package.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Access traits](/foundation/access-traits): expose array syntax and magic property access from a single set of accessor methods.
- [Option type](/foundation/option): represent an optional value with `Some` and `None` instead of relying on `null`.
- [Network: IP](/foundation/network): validate and parse IPv4 and IPv6 addresses.
- [Util classes](/foundation/utilities): static helpers for arrays, colors, math, strings, XML, reflection and debugging.
- [Singleton, Stopwatch and global functions](/foundation/singleton-and-stopwatch): shared instances, timing and process helpers.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Network\IP;
use Raxos\Foundation\Option\Option;

$ip = IP::parse('203.0.113.10');

$name = Option::fromValue($ip?->value)
    ->map(static fn(string $value): string => "client@{$value}")
    ->getOrElse('client@unknown');
```

## Installation

Install the package with Composer. See [installation](/foundation/installation) for the required PHP version and extensions.

```shell
composer require raxos/foundation
```
