---
outline: deep
---

# Generating a specification

The generator has three moving parts. A [RouterBuilder](/openapi/api/RouterBuilder) walks an existing [raxos/router](/router/) instance and fills a map of paths. A [SchemaBuilder](/openapi/api/SchemaBuilder) resolves the schemas referenced by those paths (response bodies, request models, property types). Finally an [OpenAPI](/openapi/api/OpenAPI) document ties the paths and schemas together with an info block and renders the result.

## The pipeline

You start with a router that is already built from your controllers. Give it to a `RouterBuilder` and call `build()`.

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\RouterBuilder;

$builder = new RouterBuilder($router);
$builder->build();
```

`build()` walks `router->staticRoutes` and `router->dynamicRoutes` and adds one entry to the `paths` map per route that should be documented. While doing so it uses the shared `SchemaBuilder` to resolve response and property schemas, so after `build()` returns three public properties are populated:

- `paths`: a map of path strings to `Path` definitions.
- `responses`: a map of reusable named responses (for `JsonSerializable` models).
- `schemas`: a map of reusable named schemas.

## Rendering the document

Feed those maps into a new `OpenAPI` document together with an `Info` block and optional servers and components, then render it.

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\{OpenAPI, RouterBuilder};
use Raxos\OpenAPI\Definition\{Components, Info, Server};

$builder = new RouterBuilder($router);
$builder->build();

$openapi = new OpenAPI(
    info: new Info(title: 'My API', version: '1.0.0'),
    servers: [new Server('https://api.example.com', 'Production')],
    paths: $builder->paths->toArray(),
    components: new Components(
        responses: $builder->responses->toArray(),
        schemas: $builder->schemas->toArray()
    )
);

file_put_contents('openapi.json', $openapi->getJSON());
file_put_contents('openapi.yaml', $openapi->getYAML());
```

`getJSON()` returns the document encoded as JSON and `getYAML()` returns it as YAML. Both render the same underlying structure.

## What gets skipped

A route is only documented when it has a visible handler. Concretely, a route is left out of the `paths` map when:

- the controller method has no `#[Endpoint]` attribute, or
- the controller class or the method carries a `#[Hidden]` attribute.

Paths whose operations are all skipped are not added at all, so hiding every method of a controller removes the path entirely.

## Documenting a subset of controllers

Pass a `controllers` array to restrict the builder to a fixed list of controller classes. This is useful when you generate more than one document from the same router, for example a public specification and a private one.

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\RouterBuilder;

$publicBuilder = new RouterBuilder($router, controllers: [
    MeController::class,
    ProductController::class
]);
$publicBuilder->build();
```

Any route whose controller class is not in the list is ignored, even if its method has an `#[Endpoint]` attribute.

::: tip
The `paths`, `responses` and `schemas` properties are [collection](/collection/) maps. Call `toArray()` on them before passing them to the `OpenAPI` and `Components` constructors, which expect plain arrays.
:::
