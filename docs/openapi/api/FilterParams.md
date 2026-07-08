---
outline: deep
---

# #[FilterParams]

`Raxos\OpenAPI\Attribute\FilterParams` documents the structured search or filter query parameters of an endpoint. It reflects the [raxos/search](/search/) `#[Filter]` attributes declared on the given [raxos/database](/database/) model and adds one query parameter per structured filter.

```php
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class FilterParams
```

## Constructor

```php
public function __construct(
    public string $model
)
```

| Parameter | Description |
| --- | --- |
| `model` | The `Raxos\Database\Orm\Model` class whose `#[Filter]` attributes describe the available query parameters. |

Only filters that implement `StructuredFilterInterface` contribute parameters, through their `describe()` method. A filter parameter whose name matches a parameter already present (for example a `#[MapQuery]` value) is skipped.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\Router\Attribute\{Controller, Get};

#[Controller('/products')]
final readonly class ProductController
{
    #[OpenAPI\Endpoint(summary: 'Lists the products.')]
    #[OpenAPI\FilterParams(Product::class)]
    #[Get]
    public function index(): array
    {
        // ...
    }
}
```

See [Documenting endpoints](/openapi/documenting-endpoints).
