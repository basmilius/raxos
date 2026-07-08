---
outline: deep
---

# #[Endpoint]

`Raxos\OpenAPI\Attribute\Endpoint` documents a controller method as an OpenAPI operation. A route without this attribute (or hidden through `#[Hidden]`) is left out of the generated document.

```php
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Endpoint implements AttributeInterface
```

## Constructor

```php
public function __construct(
    public ?string $summary = null,
    public ?string $description = null,
    public ?array $parameters = null,
    public ?array $tags = null,
    public ?ExternalDocumentation $externalDocs = null,
    public ?string $operationId = null,
    public ?array $security = null,
    public ?string $requestModel = null,
    public ?string $requestModelDescription = null,
    public ?bool $requestModelRequired = null,
    public ?array $responses = null
)
```

| Parameter | Description |
| --- | --- |
| `summary` | Short summary of the operation. |
| `description` | Longer CommonMark description. |
| `parameters` | Extra `#[Parameter]` definitions not discovered automatically. |
| `tags` | Tag names that group this operation, matching your `Tag` definitions. |
| `externalDocs` | An `ExternalDocumentation` link. |
| `operationId` | A unique operation identifier. |
| `security` | Security requirement names, resolving to keys in `Components::$securitySchemes`. |
| `requestModel` | An `HttpRequestModelInterface` class used as the request body. |
| `requestModelDescription` | Description of the request body. |
| `requestModelRequired` | Whether the request body is required. |
| `responses` | An array of `#[Response]` definitions, merged with any repeated `#[Response]` attributes on the method. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;
use Raxos\OpenAPI\Attribute as OpenAPI;
use Raxos\Router\Attribute\{Controller, Post};

#[Controller('/auth')]
final readonly class AuthController
{
    #[OpenAPI\Endpoint(
        summary: 'Logs a user in.',
        tags: ['Auth'],
        requestModel: LoginRequestModel::class,
        requestModelRequired: true,
        responses: [
            new OpenAPI\Response(HttpResponseCode::OK, model: Session::class),
            new OpenAPI\Response(HttpResponseCode::UNAUTHORIZED, description: 'Invalid credentials.')
        ]
    )]
    #[Post('/login')]
    public function login(LoginRequestModel $body): Session
    {
        // ...
    }
}
```

## External documentation

`externalDocs` links the operation to a page outside the specification, such as a guide or a changelog entry. It takes an `ExternalDocumentation` definition with a `description` and a `url`.

```php
use Raxos\OpenAPI\Definition\ExternalDocumentation;

#[OpenAPI\Endpoint(
    summary: 'Logs a user in.',
    externalDocs: new ExternalDocumentation('Authentication guide', 'https://docs.example.com/auth')
)]
```

See [Documenting endpoints](/openapi/documenting-endpoints) for the full picture.
