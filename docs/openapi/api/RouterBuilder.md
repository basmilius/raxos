---
outline: deep
---

# RouterBuilder

`Raxos\OpenAPI\RouterBuilder` reflects a [raxos/router](/router/) `RouterInterface` and fills a map of `Path` definitions, using a [SchemaBuilder](/openapi/api/SchemaBuilder) to resolve response and property schemas along the way.

```php
final class RouterBuilder
```

After `build()` runs, three public properties hold the results:

- `paths`: a `MapInterface` of path strings to `Path` definitions.
- `responses`: a `MapInterface` of reusable named responses (read from the shared `SchemaBuilder`).
- `schemas`: a `MapInterface` of reusable named schemas (read from the shared `SchemaBuilder`).

## Methods

### __construct

```php
public function __construct(
    public RouterInterface $router,
    public MapInterface $paths = new Map(),
    public SchemaBuilder $builder = new SchemaBuilder(),
    public ?array $controllers = null
)
```

Creates the builder for a router. Pass a `controllers` array of class strings to restrict documentation to a fixed set of controllers, useful for splitting a public and a private specification. Supply your own `paths` map or `SchemaBuilder` if you want to seed or share them.

### build

```php
public function build(): void
```

Walks `router->staticRoutes` and `router->dynamicRoutes` and populates the `paths` map. Only routes with a visible handler are added: a route is skipped when its method has no `#[Endpoint]` attribute, or when the controller or the method carries a `#[Hidden]` attribute. Path parameters are inferred from the route pattern, and `#[MapQuery]` and `#[FilterParams]` contribute query parameters.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\RouterBuilder;

$builder = new RouterBuilder($router, controllers: [MeController::class]);
$builder->build();

$paths = $builder->paths->toArray();
$schemas = $builder->schemas->toArray();
$responses = $builder->responses->toArray();
```

See [Generating a specification](/openapi/generating-a-spec) for how the maps feed into an [OpenAPI](/openapi/api/OpenAPI) document.
