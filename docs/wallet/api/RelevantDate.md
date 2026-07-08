---
outline: deep
---

# RelevantDate

`Raxos\Wallet\Apple\Component\RelevantDate` describes a single date or date range that makes a [Pass](/wallet/api/Pass) relevant on the lock screen.

```php
final readonly class RelevantDate implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

| Parameter   | Type           | Description                                     |
|-------------|----------------|--------------------------------------------------|
| `date`      | `string\|null` | A single relevant date, ISO 8601 formatted.      |
| `endDate`   | `string\|null` | End of a relevant date range, ISO 8601 formatted. |
| `startDate` | `string\|null` | Start of a relevant date range, ISO 8601 formatted. |

All three parameters are optional. Set `date` for a single moment, or `startDate` and `endDate` together for a range.

## Methods

### `jsonSerialize(): array`

Returns the relevant date data, filtering out null and empty values.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\RelevantDate;

$relevant = new RelevantDate(
    startDate: '2026-07-18T19:00:00+02:00',
    endDate: '2026-07-18T23:00:00+02:00'
);
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
- [Pass](/wallet/api/Pass)
