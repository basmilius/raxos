---
outline: deep
---

# #[Response]

`Raxos\OpenAPI\Attribute\Response` describes one possible response of an operation, identified by an `HttpResponseCode` from [raxos/http](/http/) and optionally backed by a model class. It is repeatable, so a method may declare several.

```php
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Response implements AttributeInterface
```

## Constructor

```php
public function __construct(
    public HttpResponseCode $code,
    public ?string $description = null,
    public ?string $model = null,
    public ?string $modelGeneric = null,
    public ?array $content = null
)
```

| Parameter | Description |
| --- | --- |
| `code` | The `HttpResponseCode` this response answers. |
| `description` | A description of the response. |
| `model` | The class whose schema becomes the response body. |
| `modelGeneric` | The item type when `model` is a builtin collection such as `ArrayListInterface` or `Paginated`. |
| `content` | An explicit map of media types to `MediaType` definitions. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Collection\ArrayListInterface;
use Raxos\Http\HttpResponseCode;
use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\Router\Attribute\Get;

#[OpenAPI\Endpoint(summary: 'Lists the products.')]
#[OpenAPI\Response(HttpResponseCode::OK, model: ArrayListInterface::class, modelGeneric: Product::class)]
#[OpenAPI\Response(HttpResponseCode::FORBIDDEN, description: 'You may not list products.')]
#[Get]
public function index(): ArrayList
{
    // ...
}
```

A `JsonSerializable` model is stored once as a named component and referenced afterwards, so repeated use of the same model does not duplicate the response. See [Documenting endpoints](/openapi/documenting-endpoints).

## Custom content

Pass `content` to describe a response body that is not JSON, or that needs more than one media type. It maps a media type string to a `MediaType` definition, whose `schema` is a [Schema](/openapi/api/Schema) or a reference.

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\OpenAPI\Definition\{MediaType, Schema};
use Raxos\OpenAPI\Enum\{SchemaType, StringFormat};
use Raxos\Router\Attribute\Get;

#[OpenAPI\Endpoint(summary: 'Downloads the invoice as a PDF.')]
#[OpenAPI\Response(HttpResponseCode::OK, content: [
    'application/pdf' => new MediaType(new Schema(type: SchemaType::STRING, format: StringFormat::BINARY))
])]
#[Get('/invoice.pdf')]
public function invoice(): mixed
{
    // ...
}
```

When `content` is given, `model` and `modelGeneric` are ignored.
