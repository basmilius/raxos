---
outline: deep
---

# Schema

`Raxos\OpenAPI\Definition\Schema` is the low level building block behind every schema in the generated document. [SchemaBuilder](/openapi/api/SchemaBuilder) builds most `Schema` instances for you through automatic type detection, but you construct one directly whenever you override detection with `#[Property(schema: ...)]`, as shown in [Documenting schemas](/openapi/documenting-schemas).

```php
final readonly class Schema implements DefinitionInterface
```

## Constructor

```php
public function __construct(
    public ?SchemaType $type = null,
    public ?bool $deprecated = null,
    public ?bool $nullable = null,
    public ?bool $readOnly = null,
    public ?bool $writeOnly = null,
    public ?array $allOf = null,
    public ?array $anyOf = null,
    public ?array $oneOf = null,
    public ?Schema $not = null,
    public ?int $maxLength = null,
    public ?int $minLength = null,
    public ?string $pattern = null,
    public NumberFormat|StringFormat|null $format = null,
    public ?array $enum = null,
    public ?int $maximum = null,
    public ?int $minimum = null,
    public ?bool $exclusiveMaximum = null,
    public ?bool $exclusiveMinimum = null,
    public ?int $multipleOf = null,
    public ?int $maxItems = null,
    public ?int $minItems = null,
    public ?bool $uniqueItems = null,
    public Reference|Schema|null $items = null,
    public ?array $properties = null,
    public ?array $additionalProperties = null,
    public ?array $required = null,
    public ?int $maxProperties = null,
    public ?int $minProperties = null,
)
```

| Parameter | Description |
| --- | --- |
| `type` | The JSON Schema type, from the `SchemaType` enum: `ARRAY`, `BOOLEAN`, `INTEGER`, `NULL`, `NUMBER`, `OBJECT` or `STRING`. |
| `deprecated` | Marks the schema as deprecated. |
| `nullable` | Whether `null` is a valid value. |
| `readOnly` | Whether the property is only present in responses. |
| `writeOnly` | Whether the property is only present in requests. |
| `allOf`, `anyOf`, `oneOf` | Lists of `Schema` (or `Reference`) definitions combined with the matching JSON Schema keyword. |
| `not` | A `Schema` the value must not match. |
| `maxLength`, `minLength`, `pattern` | String constraints. |
| `format` | A `NumberFormat` (`DOUBLE`, `FLOAT`, `INT32`, `INT64`) or `StringFormat` (`BINARY`, `BYTE`, `DATE`, `DATE_TIME`, `EMAIL`, `HOSTNAME`, `IPV4`, `IPV6`, `PASSWORD`, `TIME`, `URI`, `URI_REFERENCE`, `UUID`) case, depending on `type`. |
| `enum` | A fixed list of allowed values. |
| `maximum`, `minimum`, `exclusiveMaximum`, `exclusiveMinimum`, `multipleOf` | Numeric constraints. |
| `maxItems`, `minItems`, `uniqueItems`, `items` | Array constraints; `items` is the `Schema` or `Reference` of the array entries. |
| `properties`, `additionalProperties`, `required`, `maxProperties`, `minProperties` | Object constraints; `properties` maps property names to their `Schema` or `Reference`. |

## Example

A manual schema for a string with a format, built the same way [SchemaBuilder](/openapi/api/SchemaBuilder) builds one internally:

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\OpenAPI\Definition\Schema;
use Raxos\OpenAPI\Enum\{SchemaType, StringFormat};

#[OpenAPI\Property(schema: new Schema(
    type: SchemaType::STRING,
    format: StringFormat::UUID
))]
public string $id;
```

Arrays and objects compose the same way, by nesting `Schema` instances through `items` and `properties`:

```php
use Raxos\OpenAPI\Definition\Schema;
use Raxos\OpenAPI\Enum\SchemaType;

#[OpenAPI\Property(schema: new Schema(
    type: SchemaType::ARRAY,
    items: new Schema(
        type: SchemaType::OBJECT,
        properties: [
            'label' => new Schema(type: SchemaType::STRING),
            'value' => new Schema(type: SchemaType::INTEGER)
        ],
        required: ['label', 'value']
    )
))]
public array $options;
```

See [Documenting schemas](/openapi/documenting-schemas) for when automatic detection already covers the type, and this manual construction is not needed.
