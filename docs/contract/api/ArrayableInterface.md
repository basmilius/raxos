---
outline: deep
---

# ArrayableInterface

`Raxos\Contract\Collection\ArrayableInterface` is the smallest collection contract in Raxos. Anything that can be reduced to a plain array, such as models, collections and value objects, implements it, so other code can accept it without knowing the concrete type.

## Signature

```php
interface ArrayableInterface
{
    public function toArray(): array;
}
```

The interface is templated (`@template TKey of array-key`, `@template TValue`), so `toArray` is documented as returning `array<TKey, TValue>`.

## Methods

### `toArray(): array`

Returns a plain array representation of the object.

## Notes

- It is the base contract of the `Raxos\Contract\Collection` namespace, and richer contracts such as `ArrayListInterface` and `MapInterface` build on it. See [raxos/collection](/collection/).
- It is accepted as an input type across the query builder in [raxos/database](/database/), for example anywhere a `whereIn` call accepts an `ArrayableInterface` of values instead of a raw array.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Collection\ArrayableInterface;

final readonly class Coordinates implements ArrayableInterface
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
```
