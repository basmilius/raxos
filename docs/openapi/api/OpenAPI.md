---
outline: deep
---

# OpenAPI

`Raxos\OpenAPI\OpenAPI` represents the root of the specification document: the info block, servers, paths, components and tags. It renders that structure to JSON or YAML.

```php
final class OpenAPI implements DefinitionInterface
```

Constants `OpenAPI::VERSION` (`3.1.1`) and `OpenAPI::SCHEMA` (the OpenAPI JSON schema URL) are exposed for reference.

## Methods

### __construct

```php
public function __construct(
    public readonly Info $info,
    public readonly array $servers = [],
    array $paths = [],
    public readonly ?Components $components = null,
    public readonly array $tags = []
)
```

Creates the document. The `paths` array is sorted by key before it is stored, so the output is deterministic regardless of route order. `servers` is a list of `Server` definitions, and `tags` is a list of `Tag` definitions.

### getJSON

```php
public function getJSON(): string
```

Returns the specification encoded as JSON. Empty sections are omitted.

### getYAML

```php
public function getYAML(): string
```

Returns the specification encoded as YAML, rendered through `symfony/yaml`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\OpenAPI;
use Raxos\OpenAPI\Definition\{Components, Info, Server};

$openapi = new OpenAPI(
    info: new Info(title: 'My API', version: '1.0.0'),
    servers: [new Server('https://api.example.com', 'Production')],
    paths: $builder->paths->toArray(),
    components: new Components(
        responses: $builder->responses->toArray(),
        schemas: $builder->schemas->toArray()
    )
);

echo $openapi->getJSON();
```

See [Generating a specification](/openapi/generating-a-spec) for the full pipeline and [Document metadata and security](/openapi/spec-metadata) for the surrounding definitions.
