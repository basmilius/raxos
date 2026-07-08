---
outline: deep
---

# Headers and status codes

The package ships two reference types, `HttpHeader` and `HttpResponseCode`, plus a set of structure maps that back the request and response.

## Header names

`HttpHeader` is a `final` class of string constants for well known header names. Every name is lowercase, matching the way the header map normalizes keys. Use these constants instead of hardcoding strings so a typo becomes a compile time error:

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpHeader;
use Raxos\Http\Response\JsonHttpResponse;

$response = new JsonHttpResponse(['status' => 'ok']);
$response->header(HttpHeader::CACHE_CONTROL, 'no-store', replace: true);
```

The class covers permanent, provisional and non-standard headers, including entries such as `ACCEPT`, `AUTHORIZATION`, `CONTENT_TYPE`, `CONTENT_DISPOSITION`, `LOCATION`, `USER_AGENT`, `X_FORWARDED_FOR` and many more.

## Status codes

`HttpResponseCode` is a backed integer enum covering every standard status code from 100 to 511, across the informational, success, redirection, client error and server error ranges. Its `getMessage()` method returns the standard reason phrase:

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;

$code = HttpResponseCode::NOT_FOUND;

$code->value;         // 404
$code->getMessage();  // 'Not Found'
```

Responses accept a status code through their constructor or the `responseCode()` method:

```php
use Raxos\Http\Response\JsonHttpResponse;

$response = new JsonHttpResponse(['error' => 'gone']);
$response->responseCode(HttpResponseCode::GONE);
```

## Structure maps

The request and response hold their data in a set of maps under `Raxos\Http\Structure`. They extend the collection `Map` and add HTTP specific behavior.

`HttpHeadersMap` normalizes every header name to lowercase and stores each header as a list of values:

```php
$request->headers->has('content-type');   // true or false
$request->headers->get('content-type');    // the first value, or null
$request->headers->getAll('accept');       // every value as an array
```

Its writable surface is `add()`, `get()`, `getAll()`, `has()` and `set()`, where `add()` appends a value and `set()` replaces the entire entry.

`HttpCookiesMap` wraps PHP's `setcookie()`. Its `set()` accepts the usual cookie options and `unset()` removes a cookie:

```php
$response = /* ... */;
$request->cookies->set('session', $token, expires: time() + 3600, httpOnly: true);
```

The remaining maps wrap their respective superglobals: `HttpQueryMap` for the query string, `HttpPostMap` for the post body, `HttpServerMap` for the server variables and `HttpFilesMap` for uploaded files. Each is built with a `createFromGlobals()` factory when you call `HttpRequest::createFromGlobals()`.

## File uploads

`HttpFilesMap` wraps the PHP `$_FILES` superglobal. Its `createFromGlobals()` factory turns each entry into one or more [HttpFile](/http/api/HttpFile) value objects, so the map always stores a list of files per field name. A single file field yields a one element list, and a multiple file field yields one `HttpFile` per uploaded file.

An `HttpFile` is immutable and exposes the details of a single upload as public `readonly` properties:

```php
public bool $isValid;
public string $contentType;
public string $name;
public int $size;
public string $temporaryFile;
```

`isValid` is `true` only when the PHP upload error code was `UPLOAD_ERR_OK`. The other properties mirror the client filename, reported MIME type, size in bytes and the temporary path on disk.

Read a file from the map through the field name:

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;

$request = HttpRequest::createFromGlobals();

foreach ($request->files->get('documents') ?? [] as $file) {
    if (!$file->isValid) {
        continue;
    }

    move_uploaded_file($file->temporaryFile, "/storage/{$file->name}");
}
```

See [HttpFile](/http/api/HttpFile) for the full reference, and [request validation](/http/validation) for the `Upload` constraint that maps a validated property to an uploaded file.
