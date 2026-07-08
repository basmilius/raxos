---
outline: deep

cards:
    highlights:
        -   title: Exception
            code: true
            details: 'Abstract base class that pairs a machine readable error code with a human readable description and serializes to JSON.'
            link: /error/api/Exception
        -   title: ExceptionId
            code: true
            details: 'Derives a stable, reproducible numeric identifier from a class or method name.'
            link: /error/api/ExceptionId
        -   title: InvalidArgumentException
            code: true
            details: 'A ready to use exception for invalid argument errors.'
            link: /error/api/InvalidArgumentException
---

# Error

Typed, JSON serializable exceptions with stable identifiers, shared across the Raxos monorepo.

Raxos Error provides the small set of primitives that every other Raxos package builds its
exceptions on. The abstract `Exception` class pairs a machine readable error code with a human
readable description, accepts an optional previous throwable, and serializes cleanly to JSON so it
can be returned directly from an HTTP error response. `ExceptionId` derives a stable, reproducible
numeric identifier from a class or method name, removing the need to hand assign exception codes. A
ready to use `InvalidArgumentException` is included as a minimal, concrete example of the pattern.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Building custom exceptions](/error/custom-exceptions): model application specific exceptions on
  top of the abstract `Exception` class and `ExceptionId`.

## Quick example

```php
<?php
declare(strict_types=1);

namespace App\Error;

use Raxos\Error\Exception;

final class UserNotFoundException extends Exception
{
    public function __construct(string $userId)
    {
        parent::__construct(
            error: 'user_not_found',
            errorDescription: "No user exists with id {$userId}."
        );
    }
}

throw new UserNotFoundException('42');
```

Because no `$code` is passed, an `ExceptionId` is derived automatically from the class name, and the
resulting exception serializes to JSON with `code`, `error` and `error_description` keys.

## Installation

Install the package with Composer and check the requirements on the
[installation](/error/installation) page.

```shell
composer require raxos/error
```
