---
outline: deep
---

# ConstraintAttributeInterface

`Raxos\Contract\Http\Validate\ConstraintAttributeInterface` is an extension point for [raxos/http](/http/). Implement it, usually as a PHP attribute, to write a custom validation constraint that checks a request property beyond the built in constraints.

## Signature

```php
interface ConstraintAttributeInterface extends AttributeInterface
{
    public function check(ReflectionProperty $property, mixed $value): mixed;
}
```

The interface is templated (`@template TValue of mixed`).

## Methods

### `check(ReflectionProperty $property, mixed $value): mixed`

Validates `$value`, the incoming value for `$property`. Returns the value, potentially transformed, or throws a `ConstraintExceptionInterface` when it is invalid.

## Notes

- It extends the empty marker `AttributeInterface`, shared with other Http validate attributes.
- Implementations are attached directly to a request model property, next to `#[Property]` and the built in constraints described in raxos/http's validation docs.
- A single class can implement both `ConstraintAttributeInterface` and [`TransformerInterface`](/contract/api/TransformerInterface) when a value needs both checking and reshaping, for example turning a loosely typed array into a strongly typed collection.
- This is a typical extension point: raxos/http calls into it, your application supplies the implementation. See [extension points](/contract/extension-points).

## Example

```php
<?php
declare(strict_types=1);

namespace App\Http\Constraint;

use Attribute;
use Override;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Http\Validate\Error\ConstraintErrorException;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class IsHexColor implements ConstraintAttributeInterface
{
    #[Override]
    public function check(ReflectionProperty $property, mixed $value): string
    {
        if (!is_string($value) || !preg_match('/^#[0-9a-f]{6}$/i', $value)) {
            throw new ConstraintErrorException('is_hex_color', 'Expected a hex color such as #ff0000.');
        }

        return $value;
    }
}
```
