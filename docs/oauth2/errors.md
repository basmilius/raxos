---
outline: deep
---

# Error handling

Every error in the package extends the abstract `OAuth2ServerException`, which itself extends the base `Exception` from [error](/error/). Each concrete exception carries a fixed OAuth2 error code (for example `invalid_client` or `invalid_grant`) and an `HttpResponseCode`, so the router or a `Responds` based error handler can turn it into a standard OAuth2 JSON error body.

## The base exception

`OAuth2ServerException` stores the HTTP status alongside the OAuth2 error code and description:

```php
abstract class OAuth2ServerException extends Exception
{
    public function __construct(
        public readonly HttpResponseCode $responseCode,
        string $error,
        string $errorDescription,
        ?Throwable $previous = null
    )
    {
        parent::__construct($error, $errorDescription, $responseCode, $previous);
    }
}
```

The controller and grant types throw these exceptions directly; the middleware turns them into an error response through the `Responds` trait.

## The concrete exceptions

Each concrete exception fixes its HTTP status and error code, so throwing one is a single, argument free call in most cases.

| Exception | HTTP status | OAuth2 error code |
| --- | --- | --- |
| `InvalidRequestException` | 400 Bad Request | `invalid_request` |
| `InvalidClientException` | 401 Unauthorized | `invalid_client` |
| `InvalidGrantException` | 400 Bad Request | `invalid_grant` |
| `InvalidScopeException` | 400 Bad Request | `invalid_scope` |
| `InvalidTokenException` | 401 Unauthorized | `invalid_token` |
| `RedirectUriMismatchException` | 400 Bad Request | `redirect_uri_mismatch` |
| `UnsupportedGrantTypeException` | 400 Bad Request | `unsupported_grant_type` |
| `InsufficientClientScopeException` | 403 Forbidden | `insufficient_client_scope` |

`InvalidGrantException` and `InvalidScopeException` require a message argument, because it describes exactly what went wrong. The others accept an optional message and default to a standard OAuth2 description.

## Throwing an error

Inside a custom scope or token factory you throw these exceptions directly:

```php
<?php
declare(strict_types=1);

use Raxos\OAuth2\Server\Error\InvalidScopeException;

public function getScope(string $key): ScopeInterface
{
    return $this->scopes[$key] ?? throw new InvalidScopeException("Unknown scope: {$key}.");
}
```

See the [OAuth2ServerException reference](/oauth2/api/OAuth2ServerException) for the full constructor signature of every exception.
