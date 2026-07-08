---
outline: deep
---

# NFC

`Raxos\Wallet\Apple\Component\NFC` describes the NFC payload a [Pass](/wallet/api/Pass) transmits when the device is tapped against a reader.

```php
final readonly class NFC implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

| Parameter                 | Type     | Description                                                    |
|----------------------------|----------|------------------------------------------------------------------|
| `encryptionPublicKey`      | `string` | The public encryption key used by the Value Added Services protocol. |
| `message`                  | `string` | The payload transmitted to the NFC reader.                     |
| `requiresAuthentication`   | `bool`   | Whether the reader must authenticate, defaults to `false`.      |

## Methods

### `jsonSerialize(): array`

Returns the NFC data, filtering out null and empty values.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\NFC;

$nfc = new NFC(
    encryptionPublicKey: $publicKey,
    message: 'TCK-00042',
    requiresAuthentication: true
);
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
- [Pass](/wallet/api/Pass)
