---
outline: deep
---

# OAuth2Server

`Raxos\OAuth2\Server\OAuth2Server`

The composition root of the library. It bundles the client, scope and token factories and exposes the authenticated owner of the host application. Extend it once per application and implement `getOwner()` and `hasOwner()`.

## Signature

```php
abstract class OAuth2Server
{
    public const array GRANT_TYPES = [
        'authorization_code' => AuthorizationCodeGrantType::class,
        'refresh_token' => RefreshTokenGrantType::class
    ];

    public const array RESPONSE_TYPES = [
        'code' => CodeResponseType::class,
        'token' => TokenResponseType::class
    ];

    public function __construct(
        public readonly ClientFactoryInterface $clientFactory,
        public readonly ScopeFactoryInterface $scopeFactory,
        public readonly TokenFactoryInterface $tokenFactory
    ) {}

    abstract public function getOwner(): mixed;
    abstract public function hasOwner(): bool;
}
```

## Constants

| Constant | Description |
| --- | --- |
| `GRANT_TYPES` | Maps a `grant_type` string to its implementing class. The token endpoint uses this to select a grant type. |
| `RESPONSE_TYPES` | Maps a `response_type` string to its implementing class. The authorize endpoint uses this to select a response type. |

## Properties

| Property | Type | Description |
| --- | --- | --- |
| `clientFactory` | `ClientFactoryInterface` | Resolves clients by id. |
| `scopeFactory` | `ScopeFactoryInterface` | Parses, validates and resolves scopes. |
| `tokenFactory` | `TokenFactoryInterface` | Generates, stores, looks up and revokes tokens and codes. |

## Methods

### `__construct(ClientFactoryInterface $clientFactory, ScopeFactoryInterface $scopeFactory, TokenFactoryInterface $tokenFactory)`

Creates the server with its three factories.

### `getOwner(): mixed`

Abstract. Returns the currently authenticated owner, or `null` when there is none.

### `hasOwner(): bool`

Abstract. Returns `true` when an owner is available for the current request.

## Example

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

See [Server setup](/oauth2/server) for the wider picture.
