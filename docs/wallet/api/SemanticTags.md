---
outline: deep
---

# SemanticTags

`Raxos\Wallet\Apple\Component\SemanticTags` is the container for Apple's semantic tags on a [Pass](/wallet/api/Pass).

```php
final readonly class SemanticTags implements ComponentInterface
```

Implements `Raxos\Contract\Wallet\ComponentInterface` (see [raxos/contract](/contract/)).

## Constructor

`SemanticTags` has no constructor parameters.

## Methods

### `jsonSerialize(): array`

Returns an empty array. The class exists so the `semanticTags` slot on `Pass` has a typed value, but it currently exposes no configurable fields.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Component\{Pass, SemanticTags};

$pass = new Pass(
    description: 'Membership card',
    organizationName: 'Example Club',
    serialNumber: 'MBR-2026-0001',
    semanticTags: new SemanticTags()
);
```

## See also

- [Fields, barcodes and components](/wallet/fields-and-components)
- [Pass](/wallet/api/Pass)
