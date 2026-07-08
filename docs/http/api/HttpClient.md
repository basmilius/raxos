---
outline: deep
---

# HttpClient

`Raxos\Http\Client\HttpClient`

A configured Guzzle based outgoing HTTP client. It wraps a Guzzle client with a base URL, a timeout and `http_errors` disabled, and forwards the fluent request builder methods of `HttpClientRequest` through `__call()`.

```php
readonly class HttpClient
```

## Constructor

```php
public function __construct(?string $baseUrl = null, float $timeout = 5.0)
```

Creates a client with an optional base URL and a default timeout in seconds.

## Request builder

Any method that exists on `HttpClientRequest` can be called on the client; it is forwarded to a fresh request instance. That includes the fluent builder (`header()`, `basicAuth()`, `digestAuth()`, `bearerToken()`, `query()`, `json()`, `multipart()`, `options()`, `timeout()`) and the terminal verbs below.

```php
public function get(string $uri, ?array $query = null): HttpClientResponse
```

Performs a GET request, with an optional inline query.

```php
public function post(string $uri, ?array $json = null): HttpClientResponse
```

Performs a POST request, with an optional inline JSON body.

```php
public function delete(string $uri): HttpClientResponse
```

Performs a DELETE request.

Each verb returns an [HttpClientResponse](/http/api/HttpClientResponse). A transport failure throws a `RequestFailedException` that implements `Raxos\Contract\Http\HttpClientExceptionInterface`; a non-2xx response does not throw, because `http_errors` is disabled.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\Client\HttpClient;

$client = new HttpClient(baseUrl: 'https://api.example.com');

$response = $client
    ->bearerToken('secret-token')
    ->get('/users', query: ['active' => 1]);

if ($response->success()) {
    $users = $response->json();
}
```

For a POST with a JSON body:

```php
$response = $client->post('/users', json: [
    'name' => 'Bas',
    'email' => 'bas@mili.us'
]);
```

See [HTTP client](/http/http-client) for the full builder surface and the PSR bridge.
