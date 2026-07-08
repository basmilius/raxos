---
outline: deep
---

# ResponseTypeInterface

`Raxos\OAuth2\Server\ResponseType\ResponseTypeInterface`

The contract for a response type used by the authorize endpoint. After the owner approves a request, the controller resolves the incoming `response_type` in `OAuth2Server::RESPONSE_TYPES`, constructs the matching class with the token factory and calls `handle()`.

## Signature

```php
interface ResponseTypeInterface
{
    public function handle(
        HttpRequest $request,
        ClientInterface $client,
        mixed $owner,
        string $redirectUri,
        string $scope,
        ?string $state = null
    ): HttpResponse;
}
```

### `handle(HttpRequest $request, ClientInterface $client, mixed $owner, string $redirectUri, string $scope, ?string $state = null): HttpResponse`

Handles the approved authorize request and returns the redirect response back to the client.

## AbstractResponseType

`Raxos\OAuth2\Server\ResponseType\AbstractResponseType` stores the token factory and answers with a `404` response by default. Concrete response types extend it and override `handle()`.

```php
abstract class AbstractResponseType implements ResponseTypeInterface
{
    public function __construct(
        protected readonly TokenFactoryInterface $tokenFactory
    ) {}

    public function handle(HttpRequest $request, ClientInterface $client, mixed $owner, string $redirectUri, string $scope, ?string $state = null): HttpResponse
    {
        return new NotFoundHttpResponse();
    }
}
```

## Shipped response types

### CodeResponseType

Issues an authorization code and redirects back to the client with a `code` query parameter (and the original `state` when present). This is the response type of the authorization code flow.

```
{redirect_uri}?code={authorizationCode}&state={state}
```

### TokenResponseType

Issues an access token directly and redirects back to the client with the token in the URI fragment. This is the response type of the implicit flow.

```
{redirect_uri}#access_token={accessToken}&token_type=Bearer&expires_in=3600&state={state}
```

Both response types redirect with a `303 See Other` status.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OAuth2\Server\ResponseType\CodeResponseType;

$responseType = new CodeResponseType($server->tokenFactory);
$response = $responseType->handle($request, $client, $owner, $redirectUri, $scope, $state);
```

See [Authorization flow](/oauth2/authorization-flow) for how the controller selects a response type.
