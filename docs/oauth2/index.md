---
outline: deep

cards:
    highlights:
        -   title: OAuth2Server
            code: true
            details: 'The composition root that bundles the client, scope and token factories and exposes the authenticated owner.'
            link: /oauth2/api/OAuth2Server
        -   title: OAuth2Controller
            code: true
            details: 'A router attributed controller with ready made authorize, token and revoke endpoints.'
            link: /oauth2/api/OAuth2Controller
        -   title: OAuth2Middleware
            code: true
            details: 'Guards resource routes by validating a bearer access token before the request reaches the handler.'
            link: /oauth2/api/OAuth2Middleware
---

# OAuth2

Raxos OAuth2 turns a [router](/router/) application into a self-hosted OAuth2 authorization server. It ships a controller with the standard authorize, token and revoke endpoints, a middleware that guards resource routes with bearer tokens, and a set of small factory contracts (client, scope, token) that the host application implements against its own storage. The authorization code and refresh token grants and the code and token response types are implemented out of the box; everything that touches persistence is left to the application through the factory interfaces.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Server setup](/oauth2/server): implement the three factory contracts and expose the current owner through an `OAuth2Server` subclass.
- [Authorization flow](/oauth2/authorization-flow): the authorize, token and revoke endpoints exposed by `OAuth2Controller`, and how grant and response types plug into them.
- [Protecting routes](/oauth2/middleware): guard resource routes with `OAuth2Middleware` and a bearer access token.
- [Error handling](/oauth2/errors): the `OAuth2ServerException` hierarchy and how each error maps to an OAuth2 error code and HTTP status.

## Quick example

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

The three factories (client, scope and token) are passed to the parent constructor and connect the server to the host application's own storage and authentication state.

## Installation

Install the package with Composer. See [installation](/oauth2/installation) for the required PHP version and Raxos package dependencies.

```shell
composer require raxos/oauth2
```
