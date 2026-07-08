---
outline: deep
---

# Pass

`Raxos\Wallet\Apple\Component\Pass` is the readonly value object that models the full `pass.json` document. It carries the pass metadata, colors, barcodes, relevance hints and exactly one style container.

```php
final readonly class Pass implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

The constructor requires `description`, `organizationName` and `serialNumber`. Every other parameter is optional and named, mirroring Apple's PassKit schema. The most commonly used ones:

| Parameter                                                     | Type              | Description                                            |
|---------------------------------------------------------------|-------------------|--------------------------------------------------------|
| `description`                                                 | `string`          | Human readable description of the pass.                |
| `organizationName`                                            | `string`          | The organization that issued the pass.                 |
| `serialNumber`                                                | `string`          | Unique serial, also used for the `.pkpass` file name.  |
| `backgroundColor`, `foregroundColor`, `labelColor`, `footerBackgroundColor` | `Color\|null` | Colors, serialized as `rgb()` strings.        |
| `barcodes`                                                    | `Barcode[]\|null` | Barcodes shown on the pass.                            |
| `beacons`                                                     | `Beacon[]\|null`  | iBeacon proximity triggers.                            |
| `locations`                                                   | `Location[]\|null`| Geofence triggers.                                    |
| `nfc`                                                         | `NFC\|null`       | NFC payload.                                           |
| `generic`, `storeCard`, `coupon`, `eventTicket`, `boardingPass` | container\|null | The style container; set exactly one.               |
| `logoText`                                                    | `string\|null`    | Text shown next to the logo.                           |
| `relevantDate`                                                | `string\|null`    | Single date for relevance.                             |
| `relevantDates`                                               | `RelevantDate[]\|null` | Date ranges for relevance.                        |
| `expirationDate`                                              | `string\|null`    | When the pass expires.                                 |
| `voided`                                                      | `bool\|null`      | Marks the pass as void.                                |
| `webServiceURL`, `authenticationToken`                       | `string\|null`    | Web service for pass updates.                          |
| `userInfo`                                                    | `array\|null`     | Arbitrary app specific data.                           |
| `formatVersion`                                               | `int`             | Schema version, defaults to `1`.                       |

The constructor also accepts the full set of PassKit URL and venue fields (`accessibilityURL`, `addOnURL`, `appLaunchURL`, `bagPolicyURL`, `contactVenueEmail`, `directionsInformationURL`, `merchandiseURL`, `orderFoodURL`, `parkingInformationURL`, `sellURL`, `transferURL`, `transitInformationURL` and more), the store identifier arrays (`associatedStoreIdentifiers`, `auxiliaryStoreIdentifiers`), `groupingIdentifier`, `maxDistance`, `preferredStyleSchemes`, `semanticTags`, `sharingProhibited`, `suppressStripShine`, `suppressHeaderDarkening` and `useAutomaticColors`.

## Methods

### `jsonSerialize(): array`

Returns the array used to build `pass.json`, running every field through an "is not empty" filter so nulls and empty arrays are dropped. Colors are emitted as `rgb()` strings. The `passTypeIdentifier` and `teamIdentifier` are added later by [PKPass](/wallet/api/PKPass) from the [Identity](/wallet/api/Identity).

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{Barcode, EventTicket, Pass, PrimaryField};
use Raxos\Wallet\Apple\Enum\BarcodeFormat;
use Raxos\Wallet\Component\Color;

$pass = new Pass(
    description: 'Concert ticket',
    organizationName: 'Example Venue',
    serialNumber: 'TCK-00042',
    backgroundColor: Color::fromHex('#1c1c1e'),
    barcodes: [
        new Barcode(format: BarcodeFormat::QR, message: 'TCK-00042')
    ],
    eventTicket: new EventTicket(
        primaryFields: [
            new PrimaryField(key: 'event', value: 'Summer Nights', label: 'Event')
        ]
    )
);
```

## See also

- [Building a pass](/wallet/pass-structure)
- [PassFields](/wallet/api/PassFields)
- [PKPass](/wallet/api/PKPass)
