---
outline: deep
---

# Building a pass

`Raxos\Wallet\Apple\Component\Pass` is the central value object of the package. It is a `final readonly class` whose constructor properties map directly to the fields of Apple's `pass.json` document. You describe the whole pass by constructing one `Pass`, then hand it to a [PKPass](/wallet/api/PKPass) for signing.

## The Pass value object

The constructor requires three fields (`description`, `organizationName` and `serialNumber`) and accepts a long list of optional named parameters that mirror the PassKit schema: colors, barcodes, beacons, locations, relevance hints, web service configuration and the style containers.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{Generic, Pass};
use Raxos\Wallet\Component\Color;

$pass = new Pass(
    description: 'Membership card',
    organizationName: 'Example Club',
    serialNumber: 'MBR-2026-0001',
    backgroundColor: Color::fromHex('#0a84ff'),
    foregroundColor: new Color(255, 255, 255),
    labelColor: new Color(230, 230, 230),
    logoText: 'Example Club',
    generic: new Generic()
);
```

Because the class is `readonly`, a `Pass` cannot be mutated after construction. To change a pass, build a new one.

## Style containers

A pass has exactly one style, and each style is represented by its own container class. Set the matching constructor argument on the `Pass` and leave the others null:

| Style        | Constructor argument | Class                                            |
|--------------|----------------------|--------------------------------------------------|
| Generic      | `generic`            | `Raxos\Wallet\Apple\Component\Generic`           |
| Store card   | `storeCard`          | `Raxos\Wallet\Apple\Component\StoreCard`         |
| Coupon       | `coupon`             | `Raxos\Wallet\Apple\Component\Coupon`            |
| Event ticket | `eventTicket`        | `Raxos\Wallet\Apple\Component\EventTicket`       |
| Boarding pass| `boardingPass`       | `Raxos\Wallet\Apple\Component\BoardingPass`      |

`Generic`, `StoreCard`, `Coupon` and `EventTicket` are empty final classes that only extend [PassFields](/wallet/api/PassFields). They add no behavior of their own; they exist to name the style slot on the pass and to carry the field arrays.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{EventTicket, HeaderField, Pass, PrimaryField, SecondaryField};

$pass = new Pass(
    description: 'Concert ticket',
    organizationName: 'Example Venue',
    serialNumber: 'TCK-00042',
    eventTicket: new EventTicket(
        primaryFields: [
            new PrimaryField(key: 'event', value: 'Summer Nights', label: 'Event')
        ],
        secondaryFields: [
            new SecondaryField(key: 'seat', value: 'Row 4, Seat 12', label: 'Seat')
        ],
        headerFields: [
            new HeaderField(key: 'date', value: '2026-07-18', label: 'Date')
        ]
    )
);
```

### Boarding passes

`BoardingPass` extends `PassFields` too, but it additionally requires a `TransitType`, which describes the mode of travel (air, boat, bus, generic or train). It merges the transit type into its serialized output.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{BoardingPass, Pass, PrimaryField};
use Raxos\Wallet\Apple\Enum\TransitType;

$pass = new Pass(
    description: 'Flight boarding pass',
    organizationName: 'Example Air',
    serialNumber: 'BRD-77-1A',
    boardingPass: new BoardingPass(
        transitType: TransitType::AIR,
        primaryFields: new PrimaryField(key: 'gate', value: 'B12', label: 'Gate')
    )
);
```

::: info Boarding pass fields
Unlike the other containers, `BoardingPass` accepts a single field instance per slot (`primaryFields`, `secondaryFields`, `auxiliaryFields`, `additionalInfoFields`, `backFields`, `headerFields`), not an array.
:::

## Colors

`backgroundColor`, `foregroundColor`, `labelColor` and `footerBackgroundColor` all accept a `Raxos\Wallet\Component\Color` instance. When the pass is serialized, each color is rendered with its `toRgb()` helper, producing the `rgb(r, g, b)` strings Apple expects. See [Color](/wallet/api/Color) for the constructors and helpers.

## Minimal output

`jsonSerialize()` on `Pass` runs every field through an "is not empty" filter before returning, dropping nulls and empty arrays. This keeps the generated `pass.json` small and free of noise, so you only pay for the fields you actually set. The `passTypeIdentifier` and `teamIdentifier` are added later from the [Identity](/wallet/api/Identity) when `PKPass` writes `pass.json`.
