---
outline: deep
---

# HttpRequest

`Raxos\Http\HttpRequest`

An immutable wrapper around an incoming HTTP request, built from the cookies, files, headers, post, query and server maps. It implements `Raxos\Contract\Http\HttpRequestInterface`.

```php
readonly class HttpRequest implements HttpRequestInterface
```

## Properties

The request exposes its data as public `readonly` properties: `cookies`, `files`, `headers`, `post`, `query` and `server` (the structure maps), the parsed `method` ([HttpMethod](/http/api/HttpMethod)), the `pathName`, the full `uri` and a `parameters` map.

## Methods

```php
public static function create(?HttpCookiesMap $cookies = null, ?HttpFilesMap $files = null, ?HttpHeadersMap $headers = null, ?HttpPostMap $post = null, ?HttpQueryMap $query = null, ?HttpServerMap $server = null, ?HttpMethod $method = null, ?string $uri = null, Map $parameters = new Map()): self
```

Creates a request for the router, defaulting any missing part from the current global request.

```php
public static function createFromGlobals(): HttpRequestInterface
```

Builds a request entirely from the PHP superglobals.

```php
public function addParameterFromQuery(string $name, string $key, ?callable $sanitizer = null, mixed $defaultValue = null): self
```

Copies a query value into the `parameters` map, optionally sanitized, with a fallback default when the key is absent.

```php
public function bearerToken(): ?string
```

Extracts the bearer token from the `Authorization` header.

```php
public function contentType(): ?string
```

Returns the content type without any parameters.

```php
public function ip(): ?IP
```

Resolves the client IP from `cf-connecting-ip`, `x-forwarded-for` or `REMOTE_ADDR` into a [foundation](/foundation/) `IP` value object.

```php
public function isSecure(): bool
```

Returns `true` when the request was served over HTTPS.

```php
public function language(): ?string
```

Returns the first preferred language.

```php
public function languages(): array
```

Returns all accepted languages ordered by quality.

```php
public function body(): ?string
```

Returns the raw request body, or `null` when it is empty.

```php
public function json(): ?array
```

Parses the request body as JSON, throwing when the body is present but not valid JSON.

```php
public function userAgent(): ?UserAgent
```

Parses the `User-Agent` header into a [UserAgent](/http/api/UserAgent) instance.

::: tip Cached accessors
`bearerToken()`, `contentType()`, `languages()`, `body()`, `json()` and `userAgent()` cache their result per request instance, so calling them repeatedly is cheap.
:::

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;

$request = HttpRequest::createFromGlobals();

if ($request->isSecure() && $request->contentType() === 'application/json') {
    $payload = $request->json();
}

$token = $request->bearerToken();
$language = $request->language();
```
