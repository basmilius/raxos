---
outline: deep
---

# Installation

Install Wallet with Composer.

```shell
composer require raxos/wallet
```

## Requirements

- PHP 8.5 or newer.
- `ext-json` for encoding `pass.json` and the manifest.
- `ext-openssl` for signing the manifest against the issuer certificate.
- `ext-zip` for building the `.pkpass` and `.pkpasses` archives.

## Raxos dependencies

The package depends on three other Raxos packages, all pulled in automatically by Composer:

- [raxos/foundation](/foundation/): temporary files (`FileSystemUtil`) for the underlying archive and manifest, and color conversion (`ColorUtil`) for the `Color` value object.
- [raxos/http](/http/): the `HttpResponse` hierarchy that `PKPass::respond()` and `PKPassBundle::respond()` return.
- [raxos/router](/router/): available for wiring pass endpoints into a controller.

All pass components implement `Raxos\Contract\Wallet\ComponentInterface`, defined in [raxos/contract](/contract/).

## Signing certificates

Signing a pass requires a few files from the [Apple Developer portal](https://developer.apple.com):

- A **pass type identifier certificate** with its **private key** and the **password** that protects it, exported to PEM. These become the `certificate`, `privateKey` and `password` of an [Identity](/wallet/api/Identity).
- Your **team identifier** and the **pass type identifier** the certificate was issued for.
- The **Apple Worldwide Developer Relations (WWDR)** certificate.

::: warning WWDR certificate location
`PKPass::sign()` loads the WWDR certificate from `wwdr.pem` at the root of the installed package (resolved as `__DIR__ . '/../../wwdr.pem'` from `src/Apple`). Place a valid `wwdr.pem` there before signing.
:::

Return to the [Wallet introduction](/wallet/).
