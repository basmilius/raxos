---
outline: deep
---

# ORM casters

The package ships three optional casters that bridge the value objects with
[raxos/database](/database/) models. They live in the `Raxos\DateTime\Caster` namespace and each one
implements `CasterInterface` from [raxos/contract](/contract/). Because they reference the ORM
`Model`, they require [raxos/database](/database/) to be installed.

| Caster            | Value object | Timezone handling             |
|-------------------|--------------|-------------------------------|
| `DateCaster`      | `Date`       | none                          |
| `TimeCaster`      | `Time`       | decoded as UTC                |
| `DateTimeCaster`  | `DateTime`   | normalized to UTC on both ends |

## How a caster works

Each caster defines two methods:

- `decode()` turns the raw database column value into a value object, or returns `null` when the
  column is `null`.
- `encode()` turns a value object back into a plain string for storage, or returns `null`.

`DateTimeCaster` normalizes to UTC on both encode and decode, and `TimeCaster` decodes as UTC, so
stored values stay timezone consistent.

## Attaching a caster to a model

Use the `#[Caster]` attribute from [raxos/database](/database/) on the model property.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Attribute\{Caster, Column, Table};
use Raxos\Database\Orm\Model;
use Raxos\DateTime\{Date, DateTime};
use Raxos\DateTime\Caster\{DateCaster, DateTimeCaster};

#[Table('orders')]
final class Order extends Model
{
    #[Column]
    #[Caster(DateCaster::class)]
    public Date $orderedOn;

    #[Column]
    #[Caster(DateTimeCaster::class)]
    public DateTime $placedAt;
}
```

With the casters attached, reading `$order->placedAt` returns a `DateTime` instance and writing one
back stores its UTC datetime string.

Each caster pairs with the value object of the same name, documented under
[Date](/datetime/api/Date), [Time](/datetime/api/Time) and [DateTime](/datetime/api/DateTime).
