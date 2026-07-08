---
outline: deep
---

# PassFieldContent

`Raxos\Wallet\Apple\Component\PassFieldContent` is the abstract base class for the six field kinds shown on a pass. Each concrete subclass names the slot the field fills.

```php
abstract readonly class PassFieldContent implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

| Parameter           | Type                       | Description                                          |
|---------------------|----------------------------|------------------------------------------------------|
| `key`               | `string`                   | Unique key for the field.                            |
| `value`             | `string\|int`              | The displayed value.                                 |
| `attributedValue`   | `string\|null`             | Value with limited HTML markup.                      |
| `changeMessage`     | `string\|null`             | Message shown when the value changes.                |
| `currencyCode`      | `string\|null`             | ISO currency code for numeric values.                |
| `dataDetectorTypes` | `DataDetectorType[]\|null` | Detectors to apply to the value.                     |
| `dateStyle`         | `DateStyle\|null`          | How to format a date value.                          |
| `ignoresTimeZone`   | `bool\|null`               | Ignore the device time zone for dates.               |
| `isRelative`        | `bool\|null`               | Display the date relative to now.                    |
| `label`             | `string\|null`             | Label shown above the value.                         |
| `numberStyle`       | `NumberStyle\|null`        | How to format a numeric value.                       |
| `textAlignment`     | `TextAlignment\|null`      | Alignment of the field.                              |
| `timeStyle`         | `DateStyle\|null`          | How to format the time portion of a date value.      |

## Methods

### `jsonSerialize(): array`

Returns the field data, filtering out null and empty values.

## Subclasses

The six concrete field classes add no behavior beyond naming the slot:

- `PrimaryField`
- `SecondaryField`
- `AuxiliaryField`
- `AdditionalInfoField`
- `HeaderField`
- `BackField`

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\SecondaryField;
use Raxos\Wallet\Apple\Enum\{DateStyle, TextAlignment};

$field = new SecondaryField(
    key: 'valid-until',
    value: '2026-12-31',
    label: 'Valid until',
    dateStyle: DateStyle::MEDIUM,
    textAlignment: TextAlignment::RIGHT
);
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
- [PassFields](/wallet/api/PassFields)
