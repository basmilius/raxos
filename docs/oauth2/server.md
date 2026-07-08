---
outline: deep
---

# Server setup

`OAuth2Server` is the composition root of the library. It bundles the three factory contracts the server needs (clients, scopes and tokens) and exposes the currently authenticated user of the host application. Everything else in the package (the controller, the middleware, the grant and response types) reaches its dependencies through this object.

## The server class

`OAuth2Server` is abstract. You extend it once per application and implement the two owner methods, then pass the three factories to the parent constructor.

```php
<?php
declare(strict_types=1);

namespace App\OAuth2;

use App\Models\User;
use Raxos\OAuth2\Server\OAuth2Server as BaseOAuth2Server;
use function App\{currentUser, isAuthenticated};

final class OAuth2Server extends BaseOAuth2Server
{
    public function getOwner(): ?User
    {
        return currentUser();
    }

    public function hasOwner(): bool
    {
        return isAuthenticated();
    }
}
```

`getOwner()` returns the resource owner (usually the logged in user) or `null` when there is none, and `hasOwner()` returns `true` when an owner is available for the current request. The authorize endpoint uses `hasOwner()` to decide whether to render the consent screen or to redirect an unauthenticated visitor.

The constructor of the parent class accepts the three factories:

```php
public function __construct(
    public readonly ClientFactoryInterface $clientFactory,
    public readonly ScopeFactoryInterface $scopeFactory,
    public readonly TokenFactoryInterface $tokenFactory
) {}
```

They are exposed as readonly properties, so the rest of the package (and your own code) can reach them through `$server->clientFactory`, `$server->scopeFactory` and `$server->tokenFactory`.

## The client factory

`ClientFactoryInterface` resolves a `ClientInterface` by client id. The client validates its own redirect uri and secret, so the server never needs to know how your clients are stored.

```php
<?php
declare(strict_types=1);

namespace App\OAuth2;

use Raxos\OAuth2\Server\Client\{ClientFactoryInterface, ClientInterface};

final readonly class ClientFactory implements ClientFactoryInterface
{
    public function getClient(string $clientId): ?ClientInterface
    {
        return OAuthClient::find($clientId);
    }
}
```

`getClient()` returns `null` when the client does not exist. See [ClientInterface](/oauth2/api/ClientInterface) for the methods a client exposes.

## The scope factory

`ScopeFactoryInterface` parses scope strings, validates them and resolves them to `ScopeInterface` instances.

```php
<?php
declare(strict_types=1);

namespace App\OAuth2;

use Raxos\OAuth2\Server\Error\InvalidScopeException;
use Raxos\OAuth2\Server\Scope\{ScopeFactoryInterface, ScopeInterface};
use function array_map;
use function explode;

final readonly class ScopeFactory implements ScopeFactoryInterface
{
    /**
     * @return string[]
     */
    public function convertScopeString(string $scopeString): array
    {
        return explode(' ', $scopeString);
    }

    /**
     * @param string[] $scopes
     * @return ScopeInterface[]
     */
    public function convertScopes(array $scopes): array
    {
        return array_map($this->getScope(...), $scopes);
    }

    /**
     * @param string[] $scopes
     */
    public function ensureValidScopes(array $scopes): void
    {
        foreach ($scopes as $scope) {
            $this->getScope($scope);
        }
    }

    public function getScope(string $key): ScopeInterface
    {
        return Scope::tryFrom($key) ?? throw new InvalidScopeException("Unknown scope: {$key}.");
    }
}
```

`getScope()` and `ensureValidScopes()` throw an [InvalidScopeException](/oauth2/errors) for unknown scopes. See [ScopeInterface](/oauth2/api/ScopeInterface) for the details a scope exposes.

## The token factory

`TokenFactoryInterface` is the storage layer for the server. It generates, stores, looks up and revokes access tokens, refresh tokens and authorization codes. This is where the [cache](/cache/) package usually comes in. The full method list is documented in the [TokenFactoryInterface reference](/oauth2/api/TokenFactoryInterface).

## Supported grant and response types

`OAuth2Server` declares which grant and response types it understands through two constants:

```php
public const array GRANT_TYPES = [
    'authorization_code' => AuthorizationCodeGrantType::class,
    'refresh_token' => RefreshTokenGrantType::class
];

public const array RESPONSE_TYPES = [
    'code' => CodeResponseType::class,
    'token' => TokenResponseType::class
];
```

The controller looks up the incoming `grant_type` and `response_type` values in these maps and instantiates the matching class. See [Authorization flow](/oauth2/authorization-flow) for how they are used.
