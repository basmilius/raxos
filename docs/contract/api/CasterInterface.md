---
outline: deep
---

# CasterInterface

`Raxos\Contract\Database\Orm\CasterInterface` is an extension point for custom column casters in [raxos/database](/database/). Implement it to translate a raw database value into a rich PHP value and back, for example a money object or an enum.

## Signature

```php
interface CasterInterface
{
    public function decode(string|float|int|null $value, Model $instance): mixed;
    public function encode(mixed $value, Model $instance): string|float|int|null;
}
```

## Methods

### `decode(string|float|int|null $value, Model $instance): mixed`

Turns the raw string, float, int or null value stored in the database into the value exposed on the model. May throw an `OrmExceptionInterface`.

### `encode(mixed $value, Model $instance): string|float|int|null`

Does the reverse, turning the model value back into a value the database can store. May throw an `OrmExceptionInterface`.

## Notes

- Implementations are attached to a model property with raxos/database's `#[Caster]` attribute, which tells the ORM which class to call for that column.
- This is a typical extension point: raxos/database calls into it, your application supplies the implementation. See [extension points](/contract/extension-points).

## Example

```php
<?php
declare(strict_types=1);

namespace App\Casters;

use Raxos\Contract\Database\Orm\CasterInterface;
use Raxos\Database\Orm\Model;

final readonly class MoneyCaster implements CasterInterface
{
    public function decode(string|float|int|null $value, Model $instance): mixed
    {
        return $value === null ? null : (int) $value;
    }

    public function encode(mixed $value, Model $instance): string|float|int|null
    {
        return $value === null ? null : (string) $value;
    }
}
```
