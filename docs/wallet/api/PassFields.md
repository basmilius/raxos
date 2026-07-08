---
outline: deep
---

# PassFields

`Raxos\Wallet\Apple\Component\PassFields` is the abstract base class shared by the five pass style containers. It holds the field arrays that appear on a pass.

```php
abstract readonly class PassFields implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

| Parameter              | Type                          | Description                    |
|------------------------|-------------------------------|--------------------------------|
| `primaryFields`        | `PrimaryField[]\|null`        | The prominent fields.          |
| `secondaryFields`      | `SecondaryField[]\|null`      | Fields below the primary ones. |
| `additionalInfoFields` | `AdditionalInfoField[]\|null` | Extra informational fields.    |
| `auxiliaryFields`      | `AuxiliaryField[]\|null`      | Auxiliary fields.              |
| `backFields`           | `BackField[]\|null`           | Fields on the back of the pass.|
| `headerFields`         | `HeaderField[]\|null`         | Fields shown in the header.    |

## Methods

### `jsonSerialize(): array`

Returns the field arrays, filtering out any that are null or empty.

## Subclasses

`Generic`, `StoreCard`, `Coupon` and `EventTicket` are empty final classes that extend `PassFields` and add no behavior. They name the style slot on a [Pass](/wallet/api/Pass).

`BoardingPass` extends `PassFields` and adds a required `TransitType` property, merging it into `jsonSerialize()`. Its constructor takes a single field instance per slot instead of an array.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{BoardingPass, PrimaryField};
use Raxos\Wallet\Apple\Enum\TransitType;

$boardingPass = new BoardingPass(
    transitType: TransitType::TRAIN,
    primaryFields: new PrimaryField(key: 'from', value: 'Amsterdam', label: 'From')
);
```

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{EventTicket, PrimaryField, SecondaryField};

$eventTicket = new EventTicket(
    primaryFields: [
        new PrimaryField(key: 'event', value: 'Summer Nights', label: 'Event')
    ],
    secondaryFields: [
        new SecondaryField(key: 'seat', value: 'Row 4', label: 'Seat')
    ]
);
```

## See also

- [Building a pass](/wallet/pass-structure)
- [PassFieldContent](/wallet/api/PassFieldContent)
