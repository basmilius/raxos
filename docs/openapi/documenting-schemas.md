---
outline: deep
---

# Documenting schemas

Whenever an endpoint references a model (as a response body or a request model), the [SchemaBuilder](/openapi/api/SchemaBuilder) turns that PHP class into an OpenAPI schema and stores it as a reusable component. Which classes and properties are eligible is steered by the `#[Model]` and `#[Property]` attributes.

## Marking a class

`SchemaBuilder` only generates a schema for a class that carries a `#[Model]` attribute (or a compatible attribute that extends the same abstract `Schema` base). Without it, the class is not resolved and no schema is produced.

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Attribute as OpenAPI;

#[OpenAPI\Model]
final readonly class Product
{
    // ...
}
```

## Marking properties

Each property needs its own `#[Property]` attribute before it appears in the schema. Properties without one are skipped, as are ORM `#[Hidden]` properties. When present, the ORM `#[Alias]` or `#[Column]` key is used as the property name, otherwise the `alias` on `#[Property]` or the plain property name.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Http\Validate\Attribute\Property as Validate;
use Raxos\OpenAPI\Attribute as OpenAPI;

#[OpenAPI\Model]
final readonly class LoginRequestModel implements HttpRequestModelInterface
{
    public function __construct(
        #[Validate]
        #[OpenAPI\Property]
        public string $email,

        #[Validate]
        #[OpenAPI\Property]
        public string $password
    ) {}
}
```

The class level `#[Model]` attribute marks the model for schema generation, and each constructor property needs its own `#[Property]` attribute to be included in the generated schema.

## Automatic type detection

The type of a property is resolved from its PHP type declaration:

- [raxos/database](/database/) ORM models become a `$ref` to their own schema, built on first use.
- Backed enums become a string or integer schema with an `enum` list of the case values.
- `DateTimeInterface` types become a `date-time` formatted string.
- `HttpRequestModelInterface` classes are expanded into their own object schema.
- `JsonSerializable` classes are introspected through their `jsonSerialize()` method (see below).
- Remaining floats, integers and strings map to the matching scalar schema.

## JsonSerializable models

For a `JsonSerializable` class the builder reads the `#[ArrayShape]` docblock attribute on its `jsonSerialize()` method and turns each entry into a property schema. Without an `#[ArrayShape]` the class becomes a plain object schema.

```php
<?php
declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Raxos\OpenAPI\Attribute as OpenAPI;

#[OpenAPI\Model]
final readonly class Money implements JsonSerializable
{
    public function __construct(
        public int $amount,
        public string $currency
    ) {}

    #[ArrayShape(['amount' => 'int', 'currency' => 'string'])]
    public function jsonSerialize(): array
    {
        return ['amount' => $this->amount, 'currency' => $this->currency];
    }
}
```

## Overriding detection

Pass a `schema` or an `example` directly on `#[Property]` to override automatic detection. A supplied `schema` is used verbatim.

```php
use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\OpenAPI\Definition\Schema;
use Raxos\OpenAPI\Enum\SchemaType;

#[OpenAPI\Property(schema: new Schema(type: SchemaType::STRING, format: null))]
public string $token;
```

The `schema` argument accepts a full [Schema](/openapi/api/Schema) definition, including formats from the `NumberFormat` and `StringFormat` enums, enum values, and composed array or object schemas built from nested `Schema` instances. Reach for this whenever a property does not map cleanly onto one of the automatic detection rules above, for example a string that should carry a `uuid` or `email` format.

## Multiple examples

Instead of a single `example`, pass a list of `Example` definitions to `examples` on `#[Model]` or `#[Property]`. Each `Example` carries a summary, a value and optionally a description and an external value URL.

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\OpenAPI\Definition\Example;

#[OpenAPI\Property(examples: [
    new Example('Coffee mug', 'Coffee mug'),
    new Example('Water bottle', 'Water bottle', description: 'A reusable bottle.')
])]
public string $name;
```
