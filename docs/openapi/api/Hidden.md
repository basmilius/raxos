---
outline: deep
---

# #[Hidden]

`Raxos\OpenAPI\Attribute\Hidden` is a marker attribute that excludes a controller or a single method from the generated specification.

```php
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class Hidden implements AttributeInterface
```

It takes no arguments. On a controller class it hides every route the controller declares. On a method it hides that single route, even when the method also has an `#[Endpoint]` attribute.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\Router\Attribute\{Controller, Get};

#[Controller('/internal')]
#[OpenAPI\Hidden]
final readonly class InternalController
{
    #[Get('/health')]
    public function health(): array
    {
        // ...
    }
}
```

Or on a single method:

```php
#[OpenAPI\Hidden]
#[Get('/debug')]
public function debug(): array
{
    // ...
}
```

See [Documenting endpoints](/openapi/documenting-endpoints) and [Generating a specification](/openapi/generating-a-spec).
