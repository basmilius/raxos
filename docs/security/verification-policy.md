# Instance JWT verification

```php
use Raxos\Security\Jwt\JwtAlgorithm;
use Raxos\Security\Jwt\JwtVerificationKey;
use Raxos\Security\Jwt\JwtVerificationPolicy;
use Raxos\Security\Jwt\JwtVerifier;

$verifier = new JwtVerifier(new JwtVerificationPolicy(
    keys: ['current' => new JwtVerificationKey($publicKey, JwtAlgorithm::RS256)],
    issuer: 'https://issuer.example.org',
    audience: 'catalog',
    leeway: 5
));
$claims = $verifier->verify($token);
```

Each configured key binds its verification algorithm. A supplied `kid` must identify an allowed key even when only one key is configured; multiple keys require a `kid`. Tokens cannot choose a different algorithm for a configured key.

Expiration is required by default. NumericDate claims must be finite numbers. `exp`, `nbf` and `iat` use the verifier's clock and non-negative leeway in seconds. Issuer comparison is exact. Audience accepts a matching string or a list containing the configured audience; mixed and associative audience arrays are rejected.

Inject `Psr\Clock\ClockInterface` as the verifier's second argument to control time. The default `SystemClock` returns UTC. Verifiers do not modify `Jwt::$currentTime` or `Jwt::$leeway`, so separate policies can be used safely within one process. Legacy static decoding remains available; `Jwt::decodeAt()` accepts explicit time and leeway without changing global state.
