---
outline: deep
---

# Preloader

`Raxos\Foundation\Preloader` walks a set of source and vendor paths and requires every PHP file it finds, so it can be used to build an OPcache preload script.

See the [Preloader concept page](/foundation/preloader) for a guided introduction.

## Signature

```php
namespace Raxos\Foundation;

class Preloader
```

```php
public function __construct(
    array $paths = []
)
```
Creates a preloader with an optional list of initial paths to preload.

## Methods

```php
public function path(string $path): void
```
Adds a file or directory to the set of paths that will be preloaded.

```php
public function ignore(string $path): void
```
Adds a path prefix to ignore. The prefix is matched case-insensitively against the start of each candidate file path.

```php
public function preload(): void
```
Performs the actual preloading. It snapshots `get_included_files()` first, then walks every configured path and requires each PHP file that passes the filters.

::: info
The recursive directory walking, the per-file `require`, and the ignore check are internal implementation details (private methods). Only the constructor, `path()`, `ignore()` and `preload()` form the public surface.
:::

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Preloader;

require __DIR__ . '/../vendor/autoload.php';

$preloader = new Preloader([
    __DIR__ . '/../src',
]);

$preloader->path(__DIR__ . '/../vendor/raxos/database/src');
$preloader->ignore(__DIR__ . '/../src/Console');

$preloader->preload();
```
