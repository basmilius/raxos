---
outline: deep
---

# NanoId

`Raxos\Security\Id\NanoId` generates short, URL friendly, random unique identifiers.

```php
final class NanoId
```

## Methods

### `generate`

```php
public static function generate(int $length = 16): string
```

Generates a random identifier of the given length from the NanoId symbol alphabet (`_-`, digits and letters). The default length is 16 characters.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Security\Id\NanoId;

$id = NanoId::generate();
$short = NanoId::generate(12);
```
