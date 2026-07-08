---
outline: deep
---

# HttpClientResponse

`Raxos\Http\Client\HttpClientResponse`

Wraps a PSR-7 response returned by [HttpClient](/http/api/HttpClient) with convenient accessors. It is immutable and implements `Raxos\Contract\DebuggableInterface`.

```php
readonly class HttpClientResponse implements DebuggableInterface
```

## Properties

The wrapper parses three pieces of the response up front as public `readonly` properties: `protocolVersion`, `responseCode` (an [HttpResponseCode](/http/api/HttpResponseCode)) and `responseText`.

## Methods

```php
public function body(): string
```

Returns the raw response body.

```php
public function json(bool $associative = true): array|stdClass
```

Parses the response body as JSON. Returns an array by default, or a `stdClass` when `associative` is `false`. Throws `JsonException` on invalid JSON.

```php
public function stream(): StreamInterface
```

Returns the response body as a PSR-7 stream.

```php
public function header(string $name, bool $single = true): string|array
```

Returns one response header. With `single` set to `true` it returns the combined header line; with `false` it returns every value as an array.

```php
public function headers(bool $single = true): array
```

Returns all response headers.

```php
public function hasHeader(string $name): bool
```

Returns `true` when the response contains the given header.

```php
public function success(): bool
```

Returns `true` for status codes 200 to 299.

```php
public function failed(): bool
```

Returns `true` for any status code outside 200 to 299.

```php
public function clientError(): bool
```

Returns `true` for status codes 400 to 499.

```php
public function serverError(): bool
```

Returns `true` for status codes 500 to 599.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\Client\HttpClient;

$client = new HttpClient(baseUrl: 'https://api.example.com');
$response = $client->get('/users');

if ($response->success()) {
    $users = $response->json();
} elseif ($response->clientError()) {
    // 4xx: bad request, unauthorized, not found, ...
} elseif ($response->serverError()) {
    // 5xx: the upstream service failed
}
```
