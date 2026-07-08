---
outline: deep
---

# HTTP client

The outgoing client lets a Raxos application call other services. It wraps a configured Guzzle client behind a small fluent builder and a convenient response wrapper.

## Creating a client

`HttpClient` takes an optional base URL and a default timeout. Under the hood it configures a Guzzle client with `http_errors` disabled, so non-2xx responses come back as normal responses rather than exceptions:

```php
<?php
declare(strict_types=1);

use Raxos\Http\Client\HttpClient;

$client = new HttpClient(baseUrl: 'https://api.example.com', timeout: 5.0);
```

The client forwards any unknown method call to a fresh `HttpClientRequest` through `__call()`, which is why the fluent builder methods below can be called directly on the client.

## Building a request

`HttpClientRequest` exposes a fluent builder. Each method returns the request, so calls chain, and the terminal verb performs the request:

```php
$response = $client
    ->bearerToken('secret-token')
    ->header('Accept', 'application/json')
    ->query(['active' => 1])
    ->get('/users');
```

The available builder methods are:

- `header(string $name, string $value, bool $replace = true)`
- `basicAuth(string $username, string $password)`
- `digestAuth(string $username, string $password)`
- `bearerToken(string $token)`
- `query(array $query)`
- `json(array $json)`
- `multipart(array $data)`
- `options(array $options)`
- `timeout(float $timeout)`

The terminal verbs are `get(string $uri, ?array $query = null)`, `post(string $uri, ?array $json = null)` and `delete(string $uri)`. The `get()` and `post()` shortcuts accept the query or JSON body inline:

```php
$response = $client->post('/users', json: [
    'name' => 'Bas',
    'email' => 'bas@mili.us'
]);
```

## Reading the response

`HttpClientResponse` wraps the underlying PSR-7 response. It exposes the parsed `protocolVersion`, `responseCode` (an [HttpResponseCode](/http/api/HttpResponseCode)) and `responseText` as public properties, plus accessor methods:

```php
if ($response->success()) {
    $users = $response->json();
} elseif ($response->clientError()) {
    // 4xx
} elseif ($response->serverError()) {
    // 5xx
}
```

The body accessors are `body()` for the raw string, `json(bool $associative = true)` for parsed JSON and `stream()` for the PSR-7 stream. Header access is `header(string $name, bool $single = true)`, `headers(bool $single = true)` and `hasHeader(string $name)`. The status helpers are `success()`, `failed()`, `clientError()` and `serverError()`.

## Errors

Guzzle transport failures are translated into a `RequestFailedException`, which implements `Raxos\Contract\Http\HttpClientExceptionInterface`. Because `http_errors` is disabled, a 4xx or 5xx response does not throw; only a transport level failure (a timeout or a DNS error, for example) does.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Http\HttpClientExceptionInterface;
use Raxos\Http\Client\HttpClient;

$client = new HttpClient();

try {
    $response = $client->get('https://api.example.com/health');
} catch (HttpClientExceptionInterface $err) {
    // transport failure
}
```

## PSR-18 and PSR-17 bridge

For interoperability with libraries that expect PSR interfaces, `Raxos\Http\Client\Psr\Psr18Client` adapts `HttpClient` to the PSR-18 `ClientInterface`, and `HttpFactory` implements the PSR-17 request, response, server request, stream, uploaded file and URI factories. Use them when a third party library asks for a PSR client or factory instead of the Raxos client.
