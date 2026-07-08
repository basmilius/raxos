---
outline: deep
---

# OAuth2Controller

`Raxos\OAuth2\Server\OAuth2Controller`

The abstract, router attributed controller that implements the authorize, token and revoke endpoints of the OAuth2 server. Extend it, attach a route group and implement the two abstract methods. It uses the `Responds` trait from [router](/router/) to build JSON and redirect responses.

## Signature

```php
abstract readonly class OAuth2Controller
{
    use Responds;

    public function __construct(
        public OAuth2Server $oAuth2
    ) {}

    #[Get('authorize')]
    protected final function getAuthorize(HttpRequest $request): HttpResponse;

    #[Post('authorize')]
    protected final function postAuthorize(HttpRequest $request): HttpResponse;

    #[Post('revoke')]
    protected final function postRevoke(HttpRequest $request): HttpResponse;

    #[Post('token')]
    protected final function postToken(HttpRequest $request): HttpResponse;

    protected abstract function onAuthorizeMissingOwner(): HttpResponse;
    protected abstract function renderAuthorize(array $context): HttpResponse;
}
```

## Methods

### `__construct(OAuth2Server $oAuth2)`

Creates the controller for the given server.

### `getAuthorize(HttpRequest $request): HttpResponse`

`GET /authorize`. Validates `client_id`, `redirect_uri`, `response_type` and `scope`, then renders the consent screen through `renderAuthorize()`. Redirects to `onAuthorizeMissingOwner()` when there is no authenticated owner.

### `postAuthorize(HttpRequest $request): HttpResponse`

`POST /authorize`. Redirects back to the client with `error=access_denied` when the owner did not approve, otherwise delegates to the matching response type.

### `postRevoke(HttpRequest $request): HttpResponse`

`POST /revoke`. Authenticates the client and revokes the given access token, refresh token or authorization code. Answers with `202 Accepted`.

### `postToken(HttpRequest $request): HttpResponse`

`POST /token`. Authenticates the client with HTTP Basic credentials and delegates to the matching grant type.

### `onAuthorizeMissingOwner(): HttpResponse`

Abstract. Invoked when the authorize request has no authenticated owner. Typically redirects to a login page.

### `renderAuthorize(array $context): HttpResponse`

Abstract. Renders the consent screen with the given context (client, client id, redirect uri, response type, scope, resolved scopes and state).

## Example

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

See [Authorization flow](/oauth2/authorization-flow) for the full request lifecycle.
