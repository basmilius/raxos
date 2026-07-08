---
outline: deep
---

# ClientInterface

`Raxos\OAuth2\Server\Client\ClientInterface`

Represents an OAuth2 client application. The host application implements it against its own client storage. The server never reads client fields directly; it asks the client to validate its own redirect uri and secret.

## Signature

```php
interface ClientInterface
{
    public function getClientId(): string;
    public function isRedirectUriAllowed(string $redirectUri): bool;
    public function isSecretValid(string $clientSecret): bool;
}
```

## Methods

### `getClientId(): string`

Returns the client id.

### `isRedirectUriAllowed(string $redirectUri): bool`

Returns `true` when the redirect uri is allowed for this client. Used by the authorize endpoint to reject a mismatching `redirect_uri`.

### `isSecretValid(string $clientSecret): bool`

Returns `true` when the given secret matches the client. Used to authenticate the client on the token and revoke endpoints.

## ClientFactoryInterface

`Raxos\OAuth2\Server\Client\ClientFactoryInterface`

Resolves a `ClientInterface` instance by client id.

```php
interface ClientFactoryInterface
{
    public function getClient(string $clientId): ?ClientInterface;
}
```

### `getClient(string $clientId): ?ClientInterface`

Returns the client with the given id, or `null` when it does not exist.

## Example

```php
<?php
declare(strict_types=1);

namespace App\OAuth2;

use Raxos\OAuth2\Server\Client\{ClientFactoryInterface, ClientInterface};
use function hash_equals;
use function in_array;

final readonly class OAuthClient implements ClientInterface
{
    /**
     * @param string[] $redirectUris
     */
    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private array $redirectUris
    ) {}

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function isRedirectUriAllowed(string $redirectUri): bool
    {
        return in_array($redirectUri, $this->redirectUris, true);
    }

    public function isSecretValid(string $clientSecret): bool
    {
        return hash_equals($this->clientSecret, $clientSecret);
    }
}

final readonly class ClientFactory implements ClientFactoryInterface
{
    public function getClient(string $clientId): ?ClientInterface
    {
        return OAuthClient::find($clientId);
    }
}
```
