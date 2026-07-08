---
outline: deep
---

# Router middleware

`RateLimited` is an abstract middleware that applies the [rate limiting core](/rate-limit/rate-limiting)
to HTTP requests handled by [raxos/router](/router/). It implements the router's
`MiddlewareInterface`, builds a `RateLimiter` internally from the `Rate` and store you pass to its
constructor, and adds the standard `ratelimit-*` response headers to every response.

Because it does not know how to identify your callers or what a rate limited response should look
like, `RateLimited` leaves two methods abstract. You extend the class and implement them.

## Extending RateLimited

Subclasses implement `getKey`, which returns a unique identifier for the current caller, and
`getResponse`, which builds the response to send when that caller is over the limit.

```php
<?php
declare(strict_types=1);

use Raxos\Http\{HttpResponse, HttpResponseCode};
use Raxos\RateLimit\Router\RateLimited;
use Raxos\RateLimit\RateLimitStatus;

final readonly class ThrottleByIp extends RateLimited
{

    protected function getKey(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    protected function getResponse(RateLimitStatus $status): HttpResponse
    {
        return HttpResponse::json(['error' => 'Too many requests.'])
            ->withCode(HttpResponseCode::TOO_MANY_REQUESTS);
    }

}
```

::: info
The exact way you resolve a caller identifier and construct an `HttpResponse` depends on your
application. The snippet above is illustrative; see [raxos/http](/http/) and
[raxos/router](/router/) for the real request and response APIs.
:::

## How handle works

`handle` runs on every request the middleware is attached to. It calls `getStatus` on the internal
limiter for the current key, which increments the count. When the status is exceeded, it short
circuits to `getResponse` instead of calling the next handler in the chain. Otherwise it calls
`$next($request)` and decorates the result.

Regardless of the outcome, the response always receives three headers:

- `ratelimit-limit`: the configured quota.
- `ratelimit-remaining`: the remaining operations, never below 0.
- `ratelimit-reset`: the seconds until the window resets.

When the caller is over the limit, a `retry-after` header is added as well, set to the same
remaining time to live.

## Wiring it into a controller

`RateLimited` is a regular router middleware, so you attach it the way your router attaches
middleware. The most common pattern is to construct it once and pass it directly.

```php
<?php
declare(strict_types=1);

use Raxos\Cache\Redis\RedisCache;
use Raxos\RateLimit\Rate;
use Raxos\RateLimit\Store\RedisRateLimiterStore;

$store = new RedisRateLimiterStore(new RedisCache(/* ... */));
$middleware = new ThrottleByIp(Rate::minute(60), $store);
```

See [raxos/router](/router/) for how middleware is registered and executed, and
[raxos/http](/http/) for the request and response objects the middleware receives.

## Applying it as an attribute

[raxos/router](/router/) discovers middleware by scanning a controller class or action method for
any attribute instance that implements `MiddlewareInterface`. Because `RateLimited` already
implements that interface, a subclass only needs to be marked as a PHP attribute to become usable
directly on a controller, without any manual registration.

```php
<?php
declare(strict_types=1);

use Attribute;
use Raxos\Http\{HttpResponse, HttpResponseCode};
use Raxos\RateLimit\Rate;
use Raxos\RateLimit\Router\RateLimited;
use Raxos\RateLimit\RateLimitStatus;
use Raxos\RateLimit\Store\RedisRateLimiterStore;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class LoginRateLimited extends RateLimited
{

    public function __construct()
    {
        parent::__construct(
            rate: Rate::minutes(15, 10),
            store: new RedisRateLimiterStore(/* ... */)
        );
    }

    protected function getKey(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    protected function getResponse(RateLimitStatus $status): HttpResponse
    {
        return HttpResponse::json(['error' => 'Too many requests.'])
            ->withCode(HttpResponseCode::TOO_MANY_REQUESTS);
    }

}
```

With the attribute in place, protecting a controller or a single action is a one line addition:

```php
final readonly class AuthController
{

    #[LoginRateLimited]
    public function login(/* ... */): HttpResponse
    {
        // ...
    }

}
```

Because the constructor takes no arguments here, every parameter (the rate and the store) is fixed
inside the subclass. This keeps the attribute usable without arguments while still letting each
subclass configure its own rate, key prefix and response. Define one subclass per distinct limit you
need (for example one for login attempts and another for general API traffic) and apply whichever
one fits the controller or action.
