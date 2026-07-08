---
outline: deep
---

# GrantTypeInterface

`Raxos\OAuth2\Server\GrantType\GrantTypeInterface`

The contract for a grant type used by the token endpoint. The controller resolves the incoming `grant_type` in `OAuth2Server::GRANT_TYPES`, constructs the matching class with the token factory and calls `handle()`.

## Signature

```php
interface GrantTypeInterface
{
    public function handle(HttpRequest $request, ClientInterface $client): HttpResponse;
}
```

### `handle(HttpRequest $request, ClientInterface $client): HttpResponse`

Handles the token request for this grant type and returns the token response. May throw an [OAuth2ServerException](/oauth2/api/OAuth2ServerException).

## AbstractGrantType

`Raxos\OAuth2\Server\GrantType\AbstractGrantType` stores the token factory and answers with a `404` response by default. Concrete grant types extend it and override `handle()`.

```php
class AbstractGrantType implements GrantTypeInterface
{
    public function __construct(
        protected readonly TokenFactoryInterface $tokenFactory
    ) {}

    public function handle(HttpRequest $request, ClientInterface $client): HttpResponse
    {
        return new NotFoundHttpResponse();
    }
}
```

## Shipped grant types

### AuthorizationCodeGrantType

Exchanges an authorization code for an access token and a refresh token. It reads `code` and `redirect_uri` from the request, looks up the authorization code for the client, rejects it when it is expired (`invalid_grant`) or when the redirect uri does not match (`redirect_uri_mismatch`), then issues both tokens and revokes the code. The response is a JSON body:

```json
{
    "access_token": "...",
    "token_type": "Bearer",
    "scope": "...",
    "expires_in": 3600,
    "refresh_token": "..."
}
```

### RefreshTokenGrantType

Exchanges a refresh token for a new access token. It reads `refresh_token`, rejects it when it is missing or expired (`invalid_grant`), issues a new access token, and revokes the previous access token associated with that refresh token. The response is a JSON body:

```json
{
    "access_token": "...",
    "token_type": "Bearer",
    "scope": "...",
    "expires_in": 3600
}
```

Both grant types issue tokens with a fixed `expires_in` of 3600 seconds and a `Bearer` token type.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;
use Raxos\OAuth2\Server\Client\ClientInterface;
use Raxos\OAuth2\Server\GrantType\AuthorizationCodeGrantType;

$grantType = new AuthorizationCodeGrantType($server->tokenFactory);
$response = $grantType->handle($request, $client);
```

See [Authorization flow](/oauth2/authorization-flow) for how the controller selects a grant type.
