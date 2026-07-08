---
outline: deep
---

# Beacon

`Raxos\Wallet\Apple\Component\Beacon` describes an iBeacon proximity trigger that surfaces a [Pass](/wallet/api/Pass) when the device is near the beacon.

```php
final readonly class Beacon implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

| Parameter       | Type           | Description                                              |
|-----------------|----------------|-------------------------------------------------------------|
| `proximityUUID` | `string`       | The unique identifier of the beacon.                     |
| `major`         | `int\|null`    | Major value of the beacon, unsigned 16 bit.               |
| `minor`         | `int\|null`    | Minor value of the beacon, unsigned 16 bit.                |
| `relevantText`  | `string\|null` | Text shown on the lock screen when the pass is relevant. |

## Methods

### `jsonSerialize(): array`

Returns the beacon data, filtering out null and empty values.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\Beacon;

$beacon = new Beacon(
    proximityUUID: 'e2c56db5-dffb-48d2-b060-d0f5a71096e0',
    major: 1,
    minor: 42
);
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
- [Pass](/wallet/api/Pass)
