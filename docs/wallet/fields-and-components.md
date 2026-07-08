---
outline: deep
---

# Fields, barcodes and components

Beyond the `Pass` itself and its style containers, the package ships a set of smaller building blocks. Every one of them is a `final readonly class` implementing `Raxos\Contract\Wallet\ComponentInterface`, so each knows how to serialize itself into the exact shape Apple expects.

## Field content

`Raxos\Wallet\Apple\Component\PassFieldContent` is the abstract base for the six field kinds shown on a pass. The concrete subclasses add no behavior; the class you pick names the slot the value fills:

| Class                 | Slot on a style container |
|-----------------------|---------------------------|
| `PrimaryField`        | `primaryFields`           |
| `SecondaryField`      | `secondaryFields`         |
| `AuxiliaryField`      | `auxiliaryFields`         |
| `AdditionalInfoField` | `additionalInfoFields`    |
| `HeaderField`         | `headerFields`            |
| `BackField`           | `backFields`              |

Each field requires a `key` and a `string|int` `value`. Optional parameters cover labels, formatting and behavior: `attributedValue`, `changeMessage`, `currencyCode`, `dataDetectorTypes`, `dateStyle`, `ignoresTimeZone`, `isRelative`, `label`, `numberStyle`, `textAlignment` and `timeStyle`.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{BackField, PrimaryField, SecondaryField};
use Raxos\Wallet\Apple\Enum\{DateStyle, TextAlignment};

$primary = new PrimaryField(
    key: 'balance',
    value: '€ 42,00',
    label: 'Balance'
);

$secondary = new SecondaryField(
    key: 'valid-until',
    value: '2026-12-31',
    label: 'Valid until',
    dateStyle: DateStyle::MEDIUM
);

$back = new BackField(
    key: 'terms',
    value: 'Non refundable.',
    textAlignment: TextAlignment::LEFT
);
```

## Barcodes

`Barcode` combines a `BarcodeFormat` with a `message` and an optional `altText`. The `messageEncoding` defaults to `iso-8859-1`. Pass an array of barcodes to the `Pass` through its `barcodes` argument.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\Barcode;
use Raxos\Wallet\Apple\Enum\BarcodeFormat;

$barcode = new Barcode(
    format: BarcodeFormat::QR,
    message: 'TCK-00042',
    altText: 'TCK-00042'
);
```

The `BarcodeFormat` enum has four cases: `AZTEC`, `CODE128`, `PDF417` and `QR`. See the [Barcode reference](/wallet/api/Barcode) for details.

## Colors

`Raxos\Wallet\Component\Color` models an RGB color. Construct it from integer channels or from a hex string, and read it back as hex or as an `rgb()` string.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Component\Color;

$blue = new Color(10, 132, 255);
$fromHex = Color::fromHex('#0a84ff');

$blue->toHex(); // "#0a84ff"
$blue->toRgb(); // "rgb(10, 132, 255)"
```

See the [Color reference](/wallet/api/Color) for the full API.

## Locations, beacons and NFC

For passes that should surface at a place or near a device, the package provides three geofencing and proximity components:

- `Location` describes a coordinate with `latitude`, `longitude`, an optional `altitude` and `relevantText`. Pass an array through the `Pass` `locations` argument.
- `Beacon` describes an iBeacon with a `proximityUUID` and optional `major`, `minor` and `relevantText`. Pass an array through `beacons`.
- `NFC` describes an NFC payload with an `encryptionPublicKey`, a `message` and a `requiresAuthentication` flag. Pass one instance through `nfc`.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{Beacon, Location};

$location = new Location(
    latitude: 52.379189,
    longitude: 4.899431,
    relevantText: 'Welcome to the venue.'
);

$beacon = new Beacon(
    proximityUUID: 'e2c56db5-dffb-48d2-b060-d0f5a71096e0',
    major: 1,
    minor: 42
);
```

See the [Location](/wallet/api/Location), [Beacon](/wallet/api/Beacon) and [NFC](/wallet/api/NFC) references for the full constructor parameters.

## Relevance and semantics

- `RelevantDate` carries an optional `date`, `startDate` and `endDate` for date based relevance. Pass an array through the `Pass` `relevantDates` argument, or set the single `relevantDate` string.
- `SemanticTags` is the container for Apple's semantic tags. It is present in the API and serializes into the pass, though it currently exposes no configurable fields.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\RelevantDate;

$relevant = new RelevantDate(
    startDate: '2026-07-18T19:00:00+02:00',
    endDate: '2026-07-18T23:00:00+02:00'
);
```

See the [RelevantDate](/wallet/api/RelevantDate) and [SemanticTags](/wallet/api/SemanticTags) references for details.

## Enums

The formatting and behavior parameters are constrained by backed enums under `Raxos\Wallet\Apple\Enum`. Each case maps one to one to Apple's PassKit string constants.

| Enum               | Cases                                                        |
|--------------------|-------------------------------------------------------------|
| `BarcodeFormat`    | `AZTEC`, `CODE128`, `PDF417`, `QR`                          |
| `DateStyle`        | `FULL`, `LONG`, `MEDIUM`, `NONE`, `SHORT`                  |
| `NumberStyle`      | `DECIMAL`, `PERCENT`, `SCIENTIFIC`, `SPELL_OUT`            |
| `TextAlignment`    | `CENTER`, `LEFT`, `NATURAL`, `RIGHT`                       |
| `DataDetectorType` | `ADDRESS`, `CALENDAR_EVENT`, `LINK`, `PHONE_NUMBER`        |
| `TransitType`      | `AIR`, `BOAT`, `BUS`, `GENERIC`, `TRAIN`                   |
