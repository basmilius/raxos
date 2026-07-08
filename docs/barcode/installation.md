---
outline: deep
---

# Installation

Install Barcode with Composer.

```shell
composer require raxos/barcode
```

## Requirements

- PHP 8.5 or newer.
- The `ctype` extension (`ext-ctype`), used by the PDF417 encoder to detect numeric data.
- The `gd` extension (`ext-gd`), required to render barcodes to PNG. SVG rendering does not need it.
- [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode), the QR encoding library used
  internally.
- [jetbrains/phpstorm-attributes](https://github.com/JetBrains/phpstorm-attributes) for editor
  metadata.

## Raxos dependencies

- [raxos/contract](/contract/): defines the `BarcodeInterface`, `EncoderInterface` and
  `RendererInterface` contracts that the classes implement.
- [raxos/error](/error/): provides the base exception classes.
- [raxos/foundation](/foundation/): provides the `ColorUtil` helper that converts hex colors to RGB
  during rendering.

No further configuration is needed. The classes are used directly.

Return to the [Barcode introduction](/barcode/).
