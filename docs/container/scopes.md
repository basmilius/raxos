# Scoped services

A scope shares an instance within one callback and releases the container's references when that callback ends.

```php
use Raxos\Container\Container;

$container = new Container();
$container->scoped(RequestContext::class);
$container->scoped(ContextInterface::class, RequestContext::class);

$container->scope(static function (Container $container): void {
    $context = $container->get(RequestContext::class);
    handleRequest($context);
});
```

Resolving a scoped binding outside a scope throws `ScopeNotActiveException`. Nested scopes receive their own instances; leaving an inner scope restores the outer instances. Cleanup also runs when the callback throws. Objects returned from the callback or retained by application code remain alive.

Bindings use the same factory, concrete-class and tag conventions as ordinary bindings. `unbind()` removes the scoped definition and its active cached instances. A scoped concrete class with `#[Singleton]` still resolves per scope. Application singletons keep their application lifetime and should not retain request-scoped state.

Custom containers can expose this capability through `ScopedContainerInterface`. The original `ContainerInterface` remains implementable without scopes.
