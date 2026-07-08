---
outline: deep
---

# OAuth2ServerException

`Raxos\OAuth2\Server\Error\OAuth2ServerException`

The abstract base for every OAuth2 error response. It extends the base `Exception` from [error](/error/) and carries an HTTP status code alongside the OAuth2 error code and description.

## Signature

```php
abstract class OAuth2ServerException extends Exception
{
    public function __construct(
        public readonly HttpResponseCode $responseCode,
        string $error,
        string $errorDescription,
        ?Throwable $previous = null
    );
}
```

### `__construct(HttpResponseCode $responseCode, string $error, string $errorDescription, ?Throwable $previous = null)`

Creates the exception with its HTTP status, OAuth2 error code and description. Concrete subclasses call this with a fixed status and error code.

## Concrete exceptions

Every exception below lives in `Raxos\OAuth2\Server\Error`.

| Exception | HTTP status | OAuth2 error code | Message |
| --- | --- | --- | --- |
| `InvalidRequestException` | 400 | `invalid_request` | Optional; defaults to a standard description. |
| `InvalidClientException` | 401 | `invalid_client` | Optional; defaults to a standard description. |
| `InvalidGrantException` | 400 | `invalid_grant` | Required. |
| `InvalidScopeException` | 400 | `invalid_scope` | Required. |
| `InvalidTokenException` | 401 | `invalid_token` | Optional; defaults to a standard description. |
| `RedirectUriMismatchException` | 400 | `redirect_uri_mismatch` | Optional; defaults to a standard description. |
| `UnsupportedGrantTypeException` | 400 | `unsupported_grant_type` | Optional; defaults to a standard description. |
| `InsufficientClientScopeException` | 403 | `insufficient_client_scope` | Optional; defaults to a standard description. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OAuth2\Server\Error\{InvalidGrantException, InvalidRequestException};

// Fixed message, required argument.
throw new InvalidGrantException('The authorization code has expired.');

// Default message, no argument.
throw new InvalidRequestException();
```

See [Error handling](/oauth2/errors) for how these exceptions become JSON error responses.
