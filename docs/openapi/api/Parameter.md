---
outline: deep
---

# #[Parameter]

`Raxos\OpenAPI\Attribute\Parameter` describes a request parameter (query, header, cookie or path) that is not already discovered automatically from the route or from the router `#[MapQuery]` attribute.

```php
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final readonly class Parameter implements AttributeInterface
```

## Constructor

```php
public function __construct(
    public string $name,
    public In $in,
    public ?string $description = null,
    public bool $required = false,
    public bool $deprecated = false,
    public bool $allowEmptyValue = false
)
```

| Parameter | Description |
| --- | --- |
| `name` | The parameter name. |
| `in` | Where the parameter lives, from the `In` enum: `PATH`, `QUERY`, `HEADER` or `COOKIE`. |
| `description` | A description of the parameter. |
| `required` | Whether the parameter is required. |
| `deprecated` | Whether the parameter is deprecated. |
| `allowEmptyValue` | Whether an empty value is allowed. |

Parameters declared with `in: In::PATH` are ignored when collected through `#[Endpoint]`, because path parameters are always taken from the route pattern itself.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\OpenAPI\Enum\In;
use Raxos\Router\Attribute\Get;

#[OpenAPI\Endpoint(
    summary: 'Searches products.',
    parameters: [
        new OpenAPI\Parameter('X-Region', In::HEADER, description: 'Region to search in.'),
        new OpenAPI\Parameter('q', In::QUERY, description: 'Search query.', required: true)
    ]
)]
#[Get]
public function search(): array
{
    // ...
}
```

See [Documenting endpoints](/openapi/documenting-endpoints).
