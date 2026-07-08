---
outline: deep
---

# HttpResponse

`Raxos\Http\HttpResponse`

The abstract base class for all outgoing responses. It handles the header map, the status code and the send lifecycle, and implements `Raxos\Contract\Http\HttpResponseInterface`. Concrete responses live in `Raxos\Http\Response`.

```php
abstract class HttpResponse implements HttpResponseInterface
```

## Constructor

```php
public function __construct(HttpHeadersMap $headers = new HttpHeadersMap(), HttpResponseCode $responseCode = HttpResponseCode::OK)
```

Concrete responses call this from their own constructor, usually after setting a content type or other headers.

## Methods

```php
public function header(string $name, string $value, bool $replace = false): static
```

Adds a header value, or replaces the entry when `replace` is `true`. Returns the response for chaining.

```php
public function responseCode(HttpResponseCode $responseCode): static
```

Overrides the response status code.

```php
public function send(): void
```

Sends the status code and headers, then the body, and finally calls `fastcgi_finish_request()` when it is available. Subclasses provide the body by overriding the protected `sendBody()`.

## Concrete responses

| Class | Purpose |
| --- | --- |
| `JsonHttpResponse` | Encodes the body as JSON and sets `content-type: application/json`. |
| `HtmlHttpResponse` | Sends an HTML string with `content-type: text/html`. |
| `BinaryHttpResponse` | Sends a raw binary string. |
| `FileHttpResponse` | Streams a file with caching and conditional request handling. |
| `RedirectHttpResponse` | Sends a `Location` header, defaulting to status 302. |
| `NoContentHttpResponse` | An empty 204 response. |
| `NotFoundHttpResponse` | An empty 404 response. |
| `ForbiddenHttpResponse` | An empty 403 response. |
| `MethodNotAllowedHttpResponse` | A 405 response with an `Allow` header. |
| `ResultHttpResponse` | A deferred response converted with `asJson()` or `asHtml()`. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpHeader;
use Raxos\Http\HttpResponseCode;
use Raxos\Http\Response\JsonHttpResponse;

$response = new JsonHttpResponse(
    body: ['status' => 'ok'],
    responseCode: HttpResponseCode::CREATED
);

$response
    ->header(HttpHeader::CACHE_CONTROL, 'no-store', replace: true)
    ->send();
```
