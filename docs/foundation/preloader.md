---
outline: deep
---

# Preloader

`Raxos\Foundation\Preloader` walks a set of source and vendor paths and requires every PHP file it finds. Its purpose is to build an OPcache preload script: instead of listing files one by one in `opcache.preload`, you point a small script at the directories you want warmed and let the Preloader require them for you.

Unlike most Foundation classes, `Preloader` is a plain class. It is neither `final` nor `readonly`, so a project can extend it if needed. You construct it with an optional array of initial paths and then add more paths, add ignore prefixes, and finally call `preload()`.

## Building a preloader

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Preloader;

$preloader = new Preloader([
    __DIR__ . '/../src',
]);

$preloader->path(__DIR__ . '/../vendor/raxos/foundation/src');
$preloader->path(__DIR__ . '/../vendor/raxos/database/src');

$preloader->ignore(__DIR__ . '/../src/Console');

$preloader->preload();
```

- The constructor takes an optional `string[]` of paths to preload.
- `path()` adds another file or directory to the set.
- `ignore()` adds a path prefix that should be skipped.
- `preload()` runs the walk and requires everything that survives the filters.

## How preload() works

When you call `preload()`, the Preloader first records the files that are already included through `get_included_files()`. Files loaded earlier in the process, such as the Composer autoloader, are therefore never required a second time. It then walks each configured path in order.

Directories are walked recursively. Every candidate file is checked against a set of built-in filters before it is required. A file is skipped automatically when any of the following holds:

- its basename starts with a dot (dotfiles);
- its path contains `/test/` or `/tests/`;
- it is not a `.php` file;
- it ends with `autoload.php`, `.phpstorm.meta.php`, `.html.php` or `.json.php`;
- its lowercased path starts with one of the prefixes registered through `ignore()`.

After each successful `require`, the list of included files is refreshed, so a file is never required twice even when it is reachable through multiple configured paths.

::: info
The ignore check is case-insensitive: `ignore()` lowercases the prefix, and each candidate path is lowercased before it is compared.
:::

## A real preload script

A typical setup is a small CLI script, for example `dev/preload.php`, that requires the Composer autoloader, adds the project `src` directory together with the vendor packages you want warmed, adds a few ignore prefixes for test-only or dev-only files, and calls `preload()`. The resulting file list is then referenced from `php.ini`.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Preloader;

require __DIR__ . '/../vendor/autoload.php';

$preloader = new Preloader();

$preloader->path(__DIR__ . '/../src');
$preloader->path(__DIR__ . '/../vendor/raxos/foundation/src');
$preloader->path(__DIR__ . '/../vendor/raxos/database/src');
$preloader->path(__DIR__ . '/../vendor/raxos/http/src');

$preloader->ignore(__DIR__ . '/../src/Console');

$preloader->preload();
```

Point OPcache at the script from your PHP configuration:

```ini
opcache.preload=/var/www/app/dev/preload.php
opcache.preload_user=www-data
```

::: warning
Preloading requires OPcache to be enabled and runs once when the PHP process starts. Files that are still under active development are best kept out of the preloaded set, which is what the `ignore()` prefixes are for.
:::

See the [Preloader API reference](/foundation/api/Preloader) for the full method signatures.
