# OAuth security profiles

A server's default profile preserves existing grants, implicit responses and the one-hour access-token lifetime. A modern profile restricts authorization responses to `code` and requires atomic refresh rotation.

```php
use Raxos\OAuth2\Server\SecurityProfile;

$server->securityProfile(SecurityProfile::modern(accessTokenLifetime: 900));
```

Authorization-code exchange continues to require S256 PKCE and atomic code consumption. The profile does not introduce public-client authentication; the existing confidential-client authentication rules remain in effect. A custom profile can restrict grant and response allowlists and choose a positive access-token lifetime in seconds.

## Storage must own rotation

Implement `RotatingTokenFactoryInterface` on the application's persistence adapter before enabling rotation. Configuration rejects an unsupported factory. `rotateRefreshToken()` receives the client, previous token, both replacement values, narrowed scope and access-token lifetime.

The adapter must perform one transaction or equivalent atomic operation:

1. Recheck the previous token's client, expiry and active family in storage.
2. Consume that token exactly once and persist both replacement tokens in the same family.
3. Retain consumed records for replay detection. Reuse must revoke the entire family, including its access tokens, before returning `false`.

A read followed by separate delete and save calls is insufficient. The core grant calls the atomic method once and does not make legacy save or revoke calls around it. Raxos does not supply an application-specific production token store.

When two requests reuse one refresh token, only one can issue replacements; the second request revokes the family under this strict reuse policy. The successful response can therefore carry tokens that have already been revoked. Clients should serialize refresh requests. The native test demonstrates this contract with two PHP processes and a transactional SQLite adapter.

Requested scope must be a subset of the previous token's scope. Client mismatch, expiry, invalid scope and failed rotation reject the grant. Legacy factories remain usable with rotation disabled.
