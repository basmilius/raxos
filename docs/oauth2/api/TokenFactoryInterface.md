---
outline: deep
---

# TokenFactoryInterface

`Raxos\OAuth2\Server\Token\TokenFactoryInterface`

The storage layer of the server. It generates, persists, looks up and revokes access tokens, refresh tokens and authorization codes. This is the one contract that touches persistence, and a typical implementation stores its tokens in [cache](/cache/).

## Signature

```php
interface TokenFactoryInterface
{
    public function generateAccessToken(): string;
    public function generateAuthorizationCode(): string;
    public function generateRefreshToken(): string;

    public function getAccessToken(string $token): ?AccessTokenInterface;
    public function getAccessTokenByAssociatedToken(ClientInterface $client, string $token): ?AccessTokenInterface;
    public function getAuthorizationCode(ClientInterface $client, string $code): ?AuthorizationCodeInterface;
    public function getRefreshToken(ClientInterface $client, string $token): ?RefreshTokenInterface;

    public function saveAccessToken(ClientInterface $client, mixed $owner, string $scope, string $accessToken, int $expiresIn, ?string $refreshToken): void;
    public function saveAuthorizationCode(ClientInterface $client, mixed $owner, string $redirectUri, string $scope, string $authorizationCode, ?string $state = null): void;
    public function saveRefreshToken(ClientInterface $client, mixed $owner, string $scope, string $refreshToken): void;

    public function revokeAccessToken(ClientInterface $client, AccessTokenInterface $accessToken): void;
    public function revokeAuthorizationCode(ClientInterface $client, AuthorizationCodeInterface $authorizationCode): void;
    public function revokeRefreshToken(ClientInterface $client, RefreshTokenInterface $refreshToken): void;
}
```

## Generating values

| Method | Description |
| --- | --- |
| `generateAccessToken(): string` | Generates a new access token value. |
| `generateAuthorizationCode(): string` | Generates a new authorization code value. |
| `generateRefreshToken(): string` | Generates a new refresh token value. |

## Looking up tokens

| Method | Description |
| --- | --- |
| `getAccessToken(string $token): ?AccessTokenInterface` | Looks up an access token by its value, or `null`. |
| `getAccessTokenByAssociatedToken(ClientInterface $client, string $token): ?AccessTokenInterface` | Looks up the access token associated with a refresh token, used by the refresh flow to revoke the previous access token. |
| `getAuthorizationCode(ClientInterface $client, string $code): ?AuthorizationCodeInterface` | Looks up an authorization code for the client, or `null`. |
| `getRefreshToken(ClientInterface $client, string $token): ?RefreshTokenInterface` | Looks up a refresh token for the client, or `null`. |

## Persisting tokens

| Method | Description |
| --- | --- |
| `saveAccessToken(...)` | Persists a new access token for the client and owner with the given scope, lifetime and optional associated refresh token. |
| `saveAuthorizationCode(...)` | Persists a new authorization code bound to the redirect uri, scope and optional state. |
| `saveRefreshToken(...)` | Persists a new refresh token for the client and owner with the given scope. |

## Revoking tokens

| Method | Description |
| --- | --- |
| `revokeAccessToken(ClientInterface $client, AccessTokenInterface $accessToken): void` | Revokes an access token. |
| `revokeAuthorizationCode(ClientInterface $client, AuthorizationCodeInterface $authorizationCode): void` | Revokes an authorization code. |
| `revokeRefreshToken(ClientInterface $client, RefreshTokenInterface $refreshToken): void` | Revokes a refresh token. |

## Token value contracts

The lookup methods return small value objects that the host application also implements.

### TokenInterface

`Raxos\OAuth2\Server\Token\TokenInterface` is the base contract for every token.

```php
interface TokenInterface
{
    public function getClientId(): string;
    public function getOwner(): mixed;
    public function getScope(): string;
    public function getToken(): string;
    public function isExpired(): bool;
    public function isScopeAllowed(string $scope): bool;
}
```

| Method | Description |
| --- | --- |
| `getClientId(): string` | Returns the client id the token belongs to. |
| `getOwner(): mixed` | Returns the resource owner the token was issued for. |
| `getScope(): string` | Returns the scope string of the token. |
| `getToken(): string` | Returns the raw token value. |
| `isExpired(): bool` | Returns `true` when the token has expired. |
| `isScopeAllowed(string $scope): bool` | Returns `true` when the token grants the given scope. |

### AccessTokenInterface and RefreshTokenInterface

`AccessTokenInterface` and `RefreshTokenInterface` both extend `TokenInterface` without adding methods; they exist so the factory can return precise types.

### AuthorizationCodeInterface

`AuthorizationCodeInterface` extends `TokenInterface` and adds the redirect uri the code is bound to:

```php
interface AuthorizationCodeInterface extends TokenInterface
{
    public function getRedirectUri(): string;
}
```

The authorization code grant compares this value against the `redirect_uri` sent to the token endpoint.
