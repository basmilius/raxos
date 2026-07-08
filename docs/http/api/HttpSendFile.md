---
outline: deep
---

# HttpSendFile

`Raxos\Http\HttpSendFile`

Streams a file from disk with HTTP range support and optional throttling. It is the range aware alternative to `FileHttpResponse`: where the response class reads a whole file with caching and conditional request handling, `HttpSendFile` serves the body in chunks, honours a `Range` request header (returning `206 Partial Content` or `416 Range Not Satisfiable`), and can pace each chunk. That makes it a good fit for video, large downloads or any asset where reading the entire file into a response is not appropriate.

It implements the [contract](/contract/) `HttpSendFileInterface`, so an application can bind and substitute its own implementation.

```php
final class HttpSendFile implements HttpSendFileInterface
```

## Constructor

```php
public function __construct(
    public protected(set) string $path,
    public protected(set) string $contentDisposition = 'file',
    public protected(set) string $contentDispositionType = 'inline',
    public protected(set) string $contentType = 'application/octet-stream',
    public protected(set) int $bytes = 40960,
    public protected(set) float $throttle = 0.1
)
```

- `path` is the absolute path of the file to stream.
- `contentDisposition` is the filename used in the `Content-Disposition` header.
- `contentDispositionType` is the disposition type, `inline` (default) or `attachment` to force a download.
- `contentType` is the MIME type sent in the `Content-Type` header.
- `bytes` is the chunk size read and flushed per iteration, defaulting to 40960 bytes.
- `throttle` is the delay in seconds applied after each chunk, defaulting to `0.1`. Set it to `0.0` to stream as fast as possible.

Every property is publicly readable and can be adjusted through the fluent setters below.

## Methods

```php
public function setBytes(int $bytes): self
```

Sets the chunk size read per iteration and returns the instance for chaining.

```php
public function setContentDisposition(string $name, string $type): self
```

Sets both the disposition filename and its type (`inline` or `attachment`) and returns the instance.

```php
public function setContentType(string $contentType): self
```

Sets the MIME type and returns the instance.

```php
public function setThrottle(float $throttle): self
```

Sets the per chunk delay in seconds and returns the instance.

```php
public function handle(?string $rangeHeader): void
```

Writes the response headers and streams the body. Pass the incoming `Range` header value, or `null` to send the whole file. When a range is given it is parsed and validated: a malformed or out of bounds range yields `416 Range Not Satisfiable` with a `Content-Range: bytes */<size>` header, while a valid range yields `206 Partial Content` with the matching `Content-Range` and `Content-Length`. The body is read in `bytes` sized chunks, flushed, and throttled, and streaming stops cleanly when the client disconnects.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;
use Raxos\Http\HttpSendFile;

$request = HttpRequest::createFromGlobals();

$sender = new HttpSendFile('/storage/videos/intro.mp4')
    ->setContentType('video/mp4')
    ->setContentDisposition('intro.mp4', 'inline');

$sender->handle($request->headers->get('range'));
```

::: tip
The default `throttle` of `0.1` seconds paces the stream to avoid saturating bandwidth. For internal or trusted transfers set it to `0.0` with `setThrottle(0.0)`.
:::
