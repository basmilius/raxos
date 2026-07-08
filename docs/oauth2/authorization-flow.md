---
outline: deep
---

# Authorization flow

`OAuth2Controller` is an abstract, router attributed controller that implements the three standard endpoints of an OAuth2 authorization server: `/authorize`, `/token` and `/revoke`. You extend it, attach a route group and fill in two abstract methods; the grant and response types plug in automatically.

## The controller

The base controller already carries the route attributes on its actions, so the subclass only needs to declare its own route group and implement `onAuthorizeMissingOwner()` and `renderAuthorize()`.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\OAuth2\OAuth2Server;
use Raxos\Http\HttpResponse;
use Raxos\OAuth2\Server\OAuth2Controller as BaseOAuth2Controller;
use Raxos\Router\Attribute\Controller;

#[Controller('/oauth')]
final readonly class OAuth2Controller extends BaseOAuth2Controller
{
    public function __construct(OAuth2Server $server)
    {
        parent::__construct($server);
    }

    protected function onAuthorizeMissingOwner(): HttpResponse
    {
        return $this->redirect('/login');
    }

    protected function renderAuthorize(array $context): HttpResponse
    {
        return $this->json($context);
    }
}
```

The controller uses the `Responds` trait from [router](/router/) to build JSON and redirect responses.

## GET /authorize

`getAuthorize()` starts the authorization code or implicit flow. When there is no authenticated owner (`hasOwner()` returns `false`), it delegates to `onAuthorizeMissingOwner()`, which usually redirects the visitor to a login page.

Otherwise it validates the request: `client_id`, `redirect_uri`, `response_type` and `scope` are required. The client is resolved through the client factory, the redirect uri is checked against the client, the response type is checked against `OAuth2Server::RESPONSE_TYPES`, and the scope string is validated through the scope factory. When everything checks out, it calls `renderAuthorize()` with a context array:

```php
[
    'client' => $client,
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => $responseType,
    'scope' => $scope,
    'scopes' => $scopes, // ScopeInterface[] resolved from the scope string
    'state' => $state,   // string or null
]
```

Your `renderAuthorize()` implementation turns this context into a consent screen where the owner can approve or deny the request.

## POST /authorize

`postAuthorize()` handles the submitted consent screen. It validates the client again, then inspects the request body:

- When the `authorize` field is absent, the owner denied the request. The controller redirects back to the client with an `error=access_denied` query parameter (and the original `state` when present).
- When the field is present, the owner approved the request. The controller looks up the matching `ResponseTypeInterface` in `OAuth2Server::RESPONSE_TYPES` and delegates to it.

The two shipped response types are `CodeResponseType` (redirects back with an authorization `code`) and `TokenResponseType` (redirects back with an access token in the URI fragment). See [ResponseTypeInterface](/oauth2/api/ResponseTypeInterface) for the details.

## POST /token

`postToken()` is the token endpoint. It authenticates the client with HTTP Basic credentials from the `Authorization` header (see below), reads the `grant_type` field, looks up the matching `GrantTypeInterface` in `OAuth2Server::GRANT_TYPES` and delegates to it.

The two shipped grant types are `AuthorizationCodeGrantType` (exchanges an authorization code for an access and refresh token) and `RefreshTokenGrantType` (exchanges a refresh token for a fresh access token). Both issue tokens with a fixed `expires_in` of 3600 seconds and a `Bearer` token type. See [GrantTypeInterface](/oauth2/api/GrantTypeInterface) for the details.

## POST /revoke

`postRevoke()` revokes a token. It authenticates the client the same way as the token endpoint, reads the `token` field and an optional `token_type_hint`, and revokes the matching access token, refresh token or authorization code through the token factory. The hint (`access_token`, `refresh_token` or `authorization_code`) is used first; when it does not match, the controller tries each token type in turn. The endpoint answers with `202 Accepted`.

## Client authentication

Both `/token` and `/revoke` authenticate the client through the `Authorization: Basic` header. The header value is a Base64 encoded `client_id:client_secret` pair. The controller decodes it, resolves the client through the client factory and calls `isSecretValid()` on it. When the header is missing, is not of type `Basic`, is malformed, or the secret does not match, the controller throws an [InvalidClientException](/oauth2/errors).
