---
outline: deep
---

# Conventions

Every Raxos package follows the same set of conventions. Knowing them makes the source and the
examples in this documentation easier to read.

## Language level

All packages target **PHP 8.5** and declare `declare(strict_types=1)` at the top of every file,
directly after the opening tag. Imports are sorted alphabetically, with `use function` and
`use const` grouped after class imports.

## Classes

- The preferred shape is a `final readonly class`.
- A `readonly class` is used when a type must stay extendable but immutable.
- `abstract class` is reserved for genuinely abstract types.
- Constructor property promotion is the default.

```php
<?php
declare(strict_types=1);

namespace Raxos\Example;

final readonly class Point
{
    public function __construct(
        public float $x,
        public float $y = 0.0
    )
    {
    }
}
```

## Documentation

Every class, method and property in library source carries PHPDoc. Describe its behavior or the constraint a caller must know. Blocks include `@author Bas Milius <bas@mili.us>` and `@throws` where relevant. Parameter tags contain only the type and name.

Class PHPDocs always start with `Class [Name]`. An optional description follows, separated from the heading and tags by blank PHPDoc lines:

```php
/**
 * Class CacheEntry
 *
 * Distinguishes a stored null or false value from a cache miss.
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Cache\Redis
 * @since 3.3.0
 */
```

`@since` records when a member was introduced. Preserve it when changing an existing member; only new code uses `@since 3.3.0`. Tests and fixtures omit PHPDocs.

Separate methods and logical steps with whitespace. Use alphabetical imports instead of fully qualified class references, and split long signatures and compound conditions into readable lines.

## Error handling

Exceptions extend `Raxos\Error\Exception` and usually implement an interface from
[`contract`](/contract/). A stable `ExceptionId` is generated from the class name when no code is
given.

```php
<?php
declare(strict_types=1);

namespace Raxos\Example;

use Raxos\Error\Exception;
use Throwable;

final class SomethingFailedException extends Exception
{
    public function __construct(string $detail, ?Throwable $previous = null)
    {
        parent::__construct(
            error: 'something_failed',
            errorDescription: $detail,
            previous: $previous
        );
    }
}
```

See [`error`](/error/) for the base classes.

## Attributes

Configuration is expressed with PHP attributes rather than arrays or config files. Two of the most
visible examples:

- The ORM in [`database`](/database/) maps models with `#[Table]`, `#[Column]`, `#[PrimaryKey]`,
  `#[HasMany]`, `#[BelongsTo]` and more.
- The [`router`](/router/) declares routes with `#[Controller]`, `#[Get]`, `#[Post]` and friends,
  and wires values with `#[MapQuery]`, `#[MapModel]` and `#[Injected]`.

Each package documents its own attributes on its concept pages.
