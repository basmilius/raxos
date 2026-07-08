---
outline: deep
---

# Bundles and localization

Once you can sign a single pass, two more pieces round out the workflow: grouping several signed passes into one download, and translating the strings shown on a pass.

## Bundling passes

`Raxos\Wallet\Apple\PKPassBundle` collects already signed [PKPass](/wallet/api/PKPass) instances into a single `.pkpasses` archive. Construct it with the bundle file name, `add()` each signed pass, then close and respond just like a single pass.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\PKPassBundle;

$bundle = new PKPassBundle('tickets.pkpasses');

foreach ($passes as $pkpass) {
    $pkpass->sign();
    $pkpass->close();

    $bundle->add($pkpass);
}

$bundle->close();

return $bundle->respond();
```

`add()` reads the pass through its `binary()` output and stores it in the bundle archive under the pass file name. Sign and close each `PKPass` before adding it, so the bytes you add are the final, signed ones.

`PKPassBundle` mirrors `PKPass` for delivery: `binary()`, `close()`, `delete()` and `respond()` all delegate to the same internal [archive](/wallet/signing-and-packaging#the-archive). Its `respond()` sets the `application/vnd.apple.pkpasses` content type, so the whole bundle downloads as one file.

## Localizing a pass

`Raxos\Wallet\Apple\Strings` builds a single language `.strings` localization file. Construct it with a language code and, optionally, an initial map of keys to values. Add more entries with `add()`, which returns `$this` for chaining and escapes quotes, backslashes and newlines automatically.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\Strings;

$strings = new Strings('en', [
    'event' => 'Event',
])
    ->add('seat', 'Seat')
    ->add('gate', 'Gate');
```

Rendered, a `Strings` instance produces the `"key" = "value";` lines Apple expects, one per line.

## Attaching strings to a pass

`PKPass::strings()` writes a `Strings` instance to the correct localization path inside the archive, `<language>.lproj/pass.strings`, using the language from the `Strings` object.

```php
<?php
declare(strict_types=1);

use Raxos\Wallet\Apple\{PKPass, Strings};

$pkpass = new PKPass($identity, $pass);

$pkpass->strings(new Strings('en', ['event' => 'Event']));
$pkpass->strings(new Strings('nl', ['event' => 'Evenement']));

$pkpass->sign();
$pkpass->close();
```

::: warning Add strings before signing
Like assets, localization files must be added before `sign()` so they are covered by the manifest. Add one `Strings` instance per supported language.
:::
