---
outline: deep
---

# #[Model] and #[Property]

`Raxos\OpenAPI\Attribute\Model` marks a class as eligible for schema generation, and `Raxos\OpenAPI\Attribute\Property` marks the individual properties that should appear in that schema. Both extend the same abstract `Schema` base and share an identical constructor; `Model` targets the class while `Property` targets a property.

```php
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Model extends Schema

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Property extends Schema
```

## Constructor

Both attributes inherit the constructor of the abstract `Raxos\OpenAPI\Attribute\Schema`:

```php
public function __construct(
    public ?string $alias = null,
    public mixed $example = null,
    public ?array $examples = null,
    public ?Definition\Schema $schema = null
)
```

| Parameter | Description |
| --- | --- |
| `alias` | Overrides the name used for the property in the schema. |
| `example` | A single example value. |
| `examples` | A list of `Definition\Example` definitions, each with a `summary`, a `value` and an optional `description`. |
| `schema` | A [Schema](/openapi/api/Schema) definition that replaces automatic type detection entirely. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Attribute as OpenAPI;

#[OpenAPI\Model]
final readonly class Product
{
    public function __construct(
        #[OpenAPI\Property]
        public int $id,

        #[OpenAPI\Property(example: 'Coffee mug')]
        public string $name,

        #[OpenAPI\Property(alias: 'price_cents')]
        public int $priceCents
    ) {}
}
```

Properties without a `#[Property]` attribute, and ORM `#[Hidden]` properties, are skipped. See [Documenting schemas](/openapi/documenting-schemas) for how the property types are resolved, and for constructing multiple `examples` or a manual `schema`.
