---
outline: deep
---

# Strings

`Raxos\Wallet\Apple\Strings` builds a single language `.strings` localization file for a pass.

```php
final class Strings implements Stringable
```

## Constructor

| Parameter  | Type                     | Description                                    |
|------------|--------------------------|------------------------------------------------|
| `language` | `string`                 | The language code, for example `en` or `nl`.   |
| `strings`  | `array<string, string>`  | Optional initial map of keys to values.        |

The `language` property is public and read only.

## Methods

### `add(string $key, string $value): self`

Adds an entry, escaping quotes, backslashes and newlines automatically, and returns `$this` for chaining.

### `__toString(): string`

Renders the accumulated entries as `"key" = "value";` lines, separated by newlines.

## Usage

Pass a `Strings` instance to [PKPass::strings()](/wallet/api/PKPass), which writes it to `<language>.lproj/pass.strings` inside the archive. Add one `Strings` instance per supported language before calling `sign()`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Strings;

$strings = new Strings('en', [
    'event' => 'Event',
])
    ->add('seat', 'Seat')
    ->add('gate', 'Gate');

$pkpass->strings($strings);
```

## See also

- [Bundles and localization](/wallet/bundles-and-localization)
- [PKPass](/wallet/api/PKPass)
