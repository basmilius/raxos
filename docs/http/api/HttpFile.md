---
outline: deep
---

# HttpFile

`Raxos\Http\HttpFile`

Represents a single uploaded file. Each instance wraps one entry of the PHP `$_FILES` superglobal, so a form field that uploads several files results in one `HttpFile` per file. The object is immutable and implements the [contract](/contract/) `DebuggableInterface`.

```php
final readonly class HttpFile implements DebuggableInterface
```

## Constructor

```php
public function __construct(private array $file)
```

Takes one raw `$_FILES` entry (the array with the `error`, `type`, `name`, `size` and `tmp_name` keys) and derives the public properties from it. You rarely call this yourself: [HttpFilesMap](/http/headers-and-status-codes) builds the instances through its `createFromGlobals()` factory and exposes them on `HttpRequest::$files`.

## Properties

All properties are public and `readonly`.

```php
public bool $isValid;
public string $contentType;
public string $name;
public int $size;
public string $temporaryFile;
```

- `isValid` is `true` only when the underlying PHP upload error code was `UPLOAD_ERR_OK`. A failed or missing upload yields `false`.
- `contentType` is the MIME type reported by the client (`$file['type']`).
- `name` is the original client filename (`$file['name']`).
- `size` is the file size in bytes (`$file['size']`).
- `temporaryFile` is the path to the temporary upload on disk (`$file['tmp_name']`), which you move to permanent storage.

## Debug view

```php
public function __debugInfo(): array
```

Returns a `content_type`, `name`, `size` and `temporary_file` view for `var_dump()` and debuggers. The raw `$file` array is intentionally excluded.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;

$request = HttpRequest::createFromGlobals();

foreach ($request->files->get('avatar') ?? [] as $file) {
    if (!$file->isValid) {
        continue;
    }

    // $file->name, $file->contentType, $file->size
    move_uploaded_file($file->temporaryFile, "/storage/{$file->name}");
}
```

## Validation

The [Upload](/http/validation) constraint attribute checks a mapped property against this type. It rejects a value that is not an `HttpFile` or whose `isValid` is `false`, and returns the `HttpFile` otherwise:

```php
use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Http\HttpFile;
use Raxos\Http\Validate\Attribute\Property;
use Raxos\Http\Validate\Constraint\Upload;

final class UploadAvatarRequest implements HttpRequestModelInterface
{
    public function __construct(
        #[Property]
        #[Upload]
        public HttpFile $avatar
    ) {}
}
```
