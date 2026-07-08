---
outline: deep

cards:
    highlights:
        -   title: RouterBuilder
            code: true
            details: 'Walks a router and turns every documented route into an OpenAPI path.'
            link: /openapi/api/RouterBuilder
        -   title: SchemaBuilder
            code: true
            details: 'Reflects PHP classes and properties into reusable schema definitions.'
            link: /openapi/api/SchemaBuilder
        -   title: OpenAPI
            code: true
            details: 'The root document that renders the specification as JSON or YAML.'
            link: /openapi/api/OpenAPI
        -   title: '#[Endpoint]'
            code: true
            details: 'Adds summaries, tags, security and responses to a controller method.'
            link: /openapi/api/Endpoint
---

# OpenAPI

Raxos OpenAPI reflects the controllers of a [raxos/router](/router/) application and turns them into a full OpenAPI 3.1.1 document. Routes, path parameters and request models are discovered automatically from the router, while PHP attributes such as `#[Endpoint]`, `#[Response]` and `#[Model]` let you add summaries, tags, security requirements and response schemas where reflection alone is not enough. The generated document can be exported as JSON or YAML.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Generating a specification](/openapi/generating-a-spec): the pipeline from a router to a rendered document.
- [Documenting endpoints](/openapi/documenting-endpoints): annotate controller methods with `#[Endpoint]`, `#[Response]`, `#[Parameter]` and `#[FilterParams]`.
- [Documenting schemas](/openapi/documenting-schemas): turn PHP classes into OpenAPI schemas with `#[Model]` and `#[Property]`.
- [Document metadata and security](/openapi/spec-metadata): assemble the info block, servers, tags, components and security schemes.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\{OpenAPI, RouterBuilder};
use Raxos\OpenAPI\Definition\{Components, Info, Server};

$builder = new RouterBuilder($router);
$builder->build();

$openapi = new OpenAPI(
    info: new Info(
        title: 'My API',
        version: '1.0.0',
        summary: 'Public API of my application.'
    ),
    servers: [
        new Server('https://api.example.com', 'Production')
    ],
    paths: $builder->paths->toArray(),
    components: new Components(
        responses: $builder->responses->toArray(),
        schemas: $builder->schemas->toArray()
    )
);

echo $openapi->getYAML();
```

`RouterBuilder` walks every route of the router, `SchemaBuilder` resolves response and property schemas along the way, and the collected paths, responses and schemas feed straight into a new `OpenAPI` document.

## Installation

See [Installation](/openapi/installation) for the Composer command, the required PHP extensions and the Raxos packages this module depends on.
