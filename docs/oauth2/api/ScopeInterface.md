---
outline: deep
---

# ScopeInterface

`Raxos\OAuth2\Server\Scope\ScopeInterface`

Describes a single OAuth2 scope: its key, its human readable name and its description. The consent screen uses the name and description to explain to the owner what access is requested.

## Signature

```php
interface ScopeInterface
{
    public function getKey(): string;
    public function getName(): string;
    public function getDescription(): string;
}
```

## Methods

### `getKey(): string`

Returns the scope key, the value that appears in a scope string (for example `profile:read`).

### `getName(): string`

Returns the human readable name of the scope.

### `getDescription(): string`

Returns a description of what the scope grants.

## ScopeFactoryInterface

`Raxos\OAuth2\Server\Scope\ScopeFactoryInterface`

Parses, validates and resolves OAuth2 scope strings.

```php
interface ScopeFactoryInterface
{
    public function convertScopeString(string $scopeString): array;
    public function convertScopes(array $scopes): array;
    public function ensureValidScopes(array $scopes): void;
    public function getScope(string $key): ScopeInterface;
}
```

### `convertScopeString(string $scopeString): array`

Splits a space separated scope string into an array of scope keys.

### `convertScopes(array $scopes): array`

Resolves an array of scope keys to `ScopeInterface` instances. Throws an `InvalidScopeException` for an unknown key.

### `ensureValidScopes(array $scopes): void`

Throws an `InvalidScopeException` when one of the given scopes is unknown; returns nothing on success.

### `getScope(string $key): ScopeInterface`

Returns the scope for the given key, or throws an `InvalidScopeException` when it is unknown.

## Example

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
    public function convertScopeString(string $scopeString): array
    {
        return explode(' ', $scopeString);
    }

    public function convertScopes(array $scopes): array
    {
        return array_map($this->getScope(...), $scopes);
    }

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
