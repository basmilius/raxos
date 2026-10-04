# HTTP retries and public errors

## Request builders

`HttpClient::request()` returns an isolated `HttpClientRequest`. Builders support GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS, TRACE and CONNECT. `send(HttpMethod $method, string $uri)` chooses a concrete method. `ANY` is a router matcher and is rejected by the client.

`options()` configures transport options; `optionsRequest()` sends OPTIONS. The existing facade forwarding remains available and forwards only public builder methods.

## Explicit retry policy

```php
use Raxos\Http\Client\HttpClient;
use Raxos\Http\Client\RetryPolicy;

$client = new HttpClient('https://api.example.org');
$response = $client->request()
    ->retry(new RetryPolicy(maxAttempts: 3, maxElapsed: 5.0))
    ->get('/catalog');
```

Defaults retry 429, 502, 503 and 504 plus transport failures for GET, HEAD, OPTIONS, PUT, DELETE and TRACE. POST, PATCH and CONNECT require `retryUnsafe: true`. Make mutations idempotent before opting in. Both attempts and elapsed time are bounded; per-attempt request and connection timeouts are capped by the remaining budget.

Backoff starts at 0.1 seconds and caps at 2 seconds. Retry-After accepts seconds or HTTP dates. A required delay beyond the maximum delay or elapsed budget ends retries instead of retrying early. Clocks and sleeping can be injected for deterministic tests.

Seekable PSR streams, raw resources and multipart parts restart at their original positions. One non-seekable part limits the request to one attempt. The final transport exception remains the cause of `RequestFailedException`. Retry policies apply to one builder and do not alter other requests.

## Problem Details

```php
use Raxos\Http\Response\ProblemDetails;

return new ProblemDetails(
    status: 422,
    title: 'Invalid input',
    detail: 'The requested date is outside the allowed range.',
    instance: '/requests/123',
    extensions: ['field' => 'date']
)->response();
```

`response()` returns a response with `application/problem+json`. Supported 4xx and 5xx response codes are accepted. Extension fields cannot replace `type`, `title`, `status`, `detail` or `instance`.

`fromException()` omits exception messages and causes. Supply public detail explicitly; a Raxos exception can contribute its public error identifier. Returning `.response()` is an explicit choice and does not change existing result-response formats. The representation follows [RFC 9457](https://www.rfc-editor.org/rfc/rfc9457.html).
