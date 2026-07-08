---
outline: deep

cards:
    highlights:
        -   title: Pass
            code: true
            details: 'The readonly value object that models the full pass.json document.'
            link: /wallet/api/Pass
        -   title: PKPass
            code: true
            details: 'Add assets, sign the manifest and produce a signed .pkpass archive.'
            link: /wallet/api/PKPass
        -   title: PKPassBundle
            code: true
            details: 'Group multiple signed passes into a single .pkpasses bundle.'
            link: /wallet/api/PKPassBundle
        -   title: Identity
            code: true
            details: 'Carry the certificate, private key and identifiers used to sign a pass.'
            link: /wallet/api/Identity
        -   title: Barcode
            code: true
            details: 'Attach an Aztec, Code128, PDF417 or QR barcode to a pass.'
            link: /wallet/api/Barcode
        -   title: Color
            code: true
            details: 'An RGB color value object with hex and rgb() conversion helpers.'
            link: /wallet/api/Color
---

# Wallet

Raxos Wallet is a focused PHP library for generating Apple Wallet (`.pkpass`) passes. It models the PassKit JSON schema as strongly typed, readonly value objects (`Pass`, the style containers, field content, barcodes, locations and more), packages them into a signed zip archive, and exposes ready to send HTTP responses. It builds on [raxos/foundation](/foundation/) for temporary files and color utilities, and on [raxos/http](/http/) for response objects.

The pass model mirrors Apple's `pass.json` schema closely, so if you know the PassKit fields you already know the constructor parameters. `PKPass` handles adding assets, signing the manifest with an issuer certificate through OpenSSL, and producing a downloadable response. `PKPassBundle` groups multiple signed passes into a single `.pkpasses` archive.

::: info Apple only
The package description mentions both Apple Wallet and Google Wallet, but only the Apple implementation exists today. Everything in this package lives under the `Raxos\Wallet\Apple` namespace, apart from the shared `Color` value object.
:::

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Building a pass](/wallet/pass-structure): the `Pass` value object and the five style containers (Generic, StoreCard, Coupon, EventTicket, BoardingPass).
- [Fields, barcodes and components](/wallet/fields-and-components): field content, barcodes, colors, locations, beacons, NFC, relevant dates and the enums that constrain their values.
- [Signing and packaging](/wallet/signing-and-packaging): the `PKPass` lifecycle, from adding assets to signing the manifest and closing the archive.
- [Bundles and localization](/wallet/bundles-and-localization): group passes with `PKPassBundle` and add localized `.strings` files.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\{Identity, PKPass};
use Raxos\Wallet\Apple\Component\{Barcode, EventTicket, Pass, PrimaryField};
use Raxos\Wallet\Apple\Enum\BarcodeFormat;
use Raxos\Wallet\Component\Color;

$identity = new Identity(
    certificate: file_get_contents(__DIR__ . '/certificate.pem'),
    privateKey: file_get_contents(__DIR__ . '/private-key.pem'),
    password: 'secret',
    passTypeIdentifier: 'pass.dev.example.ticket',
    teamIdentifier: 'ABCDE12345'
);

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
            new PrimaryField(key: 'event-name', value: 'Summer Nights', label: 'Event')
        ]
    )
);

$pkpass = new PKPass($identity, $pass);
$pkpass->file('icon.png', __DIR__ . '/assets/icon.png');
$pkpass->sign();
$pkpass->close();

return $pkpass->respond();
```

## Installation

Install the package with Composer. See [installation](/wallet/installation) for the required PHP version, extensions and the WWDR certificate needed for signing.

```shell
composer require raxos/wallet
```
