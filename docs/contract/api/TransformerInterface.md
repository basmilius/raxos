---
outline: deep
---

# TransformerInterface

`Raxos\Contract\Http\Validate\TransformerInterface` is an extension point for [raxos/http](/http/). Implement it, usually as a PHP attribute, to reshape an incoming request value into the type your application actually wants to work with.

## Signature

```php
interface TransformerInterface
{
    public function transform(mixed $value): mixed;
}
```

The interface is templated (`@template T of mixed`), so an implementation is documented as returning `T`.

## Methods

### `transform(mixed $value): mixed`

Turns the raw, decoded request value into the target value. Throws a `TransformerExceptionInterface` when the input cannot be transformed.

## Notes

- Transformers run as part of raxos/http's request model validation, next to the constraints described in raxos/http's validation docs.
- A single class can implement both `TransformerInterface` and [`ConstraintAttributeInterface`](/contract/api/ConstraintAttributeInterface), so a value can be checked and reshaped by the same attribute.
- This is a typical extension point: raxos/http calls into it, your application supplies the implementation. See [extension points](/contract/extension-points).

## Example

```php
<?php
declare(strict_types=1);

namespace App\Http\Constraint;

use Attribute;
use Override;
use Raxos\Contract\Http\Validate\TransformerInterface;
use Raxos\Http\Validate\Error\InvalidValueTransformerException;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class CommaSeparatedList implements TransformerInterface
{
    #[Override]
    public function transform(mixed $value): array
    {
        if (!is_string($value)) {
            throw new InvalidValueTransformerException('Expected a comma separated string.');
        }

        return array_map(trim(...), explode(',', $value));
    }
}
```
