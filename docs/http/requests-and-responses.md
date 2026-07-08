---
outline: deep
---

# Requests and responses

The two core value objects of the package are `HttpRequest`, which wraps an incoming request, and `HttpResponse`, the abstract base for every outgoing response.

## The incoming request

`HttpRequest` is an immutable (`readonly`) object built from the PHP superglobals. Build one with `createFromGlobals()`:

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;

$request = HttpRequest::createFromGlobals();
```

The request exposes a set of typed maps as public properties: `cookies`, `files`, `headers`, `post`, `query` and `server`, along with the parsed `method` (an [HttpMethod](/http/api/HttpMethod)), the `pathName`, the full `uri` and a `parameters` map that the router fills with route parameters.

### Convenience accessors

On top of the raw maps, `HttpRequest` offers a set of accessors that parse common pieces of the request. Each one is cached per request instance, so calling it more than once does no extra work:

```php
$token = $request->bearerToken();       // the token from the Authorization header
$type = $request->contentType();        // content type without parameters
$ip = $request->ip();                   // a Raxos\Foundation\Network\IP or null
$secure = $request->isSecure();         // true when served over HTTPS
$language = $request->language();        // first preferred language
$languages = $request->languages();     // all accepted languages by quality
$agent = $request->userAgent();         // a UserAgent instance or null
```

The raw body is available through `body()`, and `json()` parses that body as a JSON array. When the body is present but is not valid JSON, `json()` throws.

```php
if ($request->contentType() === 'application/json') {
    $payload = $request->json();
}
```

### Copying query values into parameters

`addParameterFromQuery()` copies a value from the query string into the `parameters` map, optionally running it through a sanitizer and falling back to a default when the key is absent:

```php
$request->addParameterFromQuery(
    name: 'page',
    key: 'p',
    sanitizer: static fn(string $value): int => max(1, (int)$value),
    defaultValue: 1
);
```

## The outgoing response

`HttpResponse` is an abstract class that handles the response code, the header map and the send lifecycle. Every concrete response extends it and lives in `Raxos\Http\Response`.

Three methods make up the shared surface:

- `header(string $name, string $value, bool $replace = false)` adds or replaces a header and returns the response for chaining.
- `responseCode(HttpResponseCode $responseCode)` overrides the status code.
- `send()` writes the status code, headers and body, then finishes the FastCGI request when that function is available.

### Response types

| Class | Purpose |
| --- | --- |
| `JsonHttpResponse` | Encodes the body as JSON and sets `content-type: application/json`. |
| `HtmlHttpResponse` | Sends an HTML string with `content-type: text/html`. |
| `BinaryHttpResponse` | Sends a raw binary string. |
| `FileHttpResponse` | Streams a file from disk with caching and conditional request handling. |
| `RedirectHttpResponse` | Sends a `Location` header, defaulting to status 302. |
| `NoContentHttpResponse` | An empty response with status 204. |
| `NotFoundHttpResponse` | An empty response with status 404. |
| `ForbiddenHttpResponse` | An empty response with status 403. |
| `MethodNotAllowedHttpResponse` | A 405 response with an `Allow` header. |
| `ResultHttpResponse` | A deferred response converted with `asJson()` or `asHtml()`. |

A typical JSON response:

```php
<?php
declare(strict_types=1);

use Raxos\Http\Response\JsonHttpResponse;
use Raxos\Http\HttpResponseCode;

$response = new JsonHttpResponse(
    body: ['status' => 'ok'],
    responseCode: HttpResponseCode::CREATED
);

$response->send();
```

### Deferred results

`ResultHttpResponse` holds a raw result and defers the choice of representation. Calling `send()` on it directly throws; instead convert it into a concrete response first:

```php
<?php
declare(strict_types=1);

use Raxos\Http\Response\ResultHttpResponse;

$result = new ResultHttpResponse(['status' => 'ok']);

$result->asJson()->send();
// or
$result->asHtml()->send();
```

## Streaming large files

`FileHttpResponse` reads a whole file with caching and conditional request handling, which is ideal for small to medium assets. For large downloads or media that a client seeks through, use `HttpSendFile` instead. It streams the file in chunks, honours a `Range` request header (`206 Partial Content` or `416 Range Not Satisfiable`), and throttles each chunk so a single download does not saturate the connection.

Construct it with the file path and stream it by passing the incoming `Range` header to `handle()`:

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;
use Raxos\Http\HttpSendFile;

$request = HttpRequest::createFromGlobals();

$sender = new HttpSendFile('/storage/videos/intro.mp4')
    ->setContentType('video/mp4')
    ->setContentDisposition('intro.mp4', 'attachment');

$sender->handle($request->headers->get('range'));
```

The fluent setters `setBytes()`, `setContentDisposition()`, `setContentType()` and `setThrottle()` tune the chunk size, disposition, MIME type and per chunk delay. See [HttpSendFile](/http/api/HttpSendFile) for the full reference.

## Related

- [Headers and status codes](/http/headers-and-status-codes) covers the header constants and the status enum used above.
- [router](/router/) builds its controller actions on top of `HttpRequest` and these response classes.
