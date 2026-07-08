---
outline: deep
---

# Location

`Raxos\Wallet\Apple\Component\Location` describes a geofence that surfaces a [Pass](/wallet/api/Pass) on the lock screen when the device is near a coordinate.

```php
final readonly class Location implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

| Parameter      | Type           | Description                                             |
|----------------|----------------|-----------------------------------------------------------|
| `latitude`     | `float`        | Latitude of the location, in degrees.                    |
| `longitude`    | `float`        | Longitude of the location, in degrees.                   |
| `altitude`     | `float\|null`  | Altitude of the location, in meters.                     |
| `relevantText` | `string\|null` | Text shown on the lock screen when the pass is relevant. |

## Methods

### `jsonSerialize(): array`

Returns the location data, filtering out null and empty values.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\Location;

$location = new Location(
    latitude: 52.379189,
    longitude: 4.899431,
    relevantText: 'Welcome to the venue.'
);
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
- [Pass](/wallet/api/Pass)
