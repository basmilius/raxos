---
outline: deep
---

# SchemaBuilder

`Raxos\OpenAPI\SchemaBuilder` turns PHP classes and properties into OpenAPI schema or reference definitions. It caches built schemas and named responses, so repeated references to the same class reuse a single component.

```php
final readonly class SchemaBuilder
```

The two public maps `responses` and `schemas` accumulate the named components and are exposed by [RouterBuilder](/openapi/api/RouterBuilder) as its own `responses` and `schemas` properties.

## Methods

### __construct

```php
public function __construct(
    public private(set) MapInterface $responses = new Map(),
    public private(set) MapInterface $schemas = new Map()
)
```

Creates the builder with empty or pre-seeded response and schema maps.

### build

```php
public function build(string $class, bool $nullable = false): void
```

Builds and stores the schema for a class that carries a `#[Model]` attribute (or a compatible `#[Schema]` attribute). Classes without such an attribute are ignored.

### reference

```php
public function reference(string $class, bool $nullable = false): Reference|Schema|null
```

Returns a `$ref` pointing at the class's schema, building it on first use. When `nullable` is true the reference is wrapped in an `anyOf` with a nullable schema. Returns `null` when the class cannot be resolved.

### response

```php
public function response(Attr\Response $responseAttr): Reference|Response|Schema|null
```

Resolves a `#[Response]` attribute into a response definition. Builtin collection types are expanded inline, and a `JsonSerializable` model is stored once as a named response and referenced afterwards.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\SchemaBuilder;

$builder = new SchemaBuilder();
$reference = $builder->reference(Product::class);

$schemas = $builder->schemas->toArray();
```

See [Documenting schemas](/openapi/documenting-schemas) for how types are detected.
