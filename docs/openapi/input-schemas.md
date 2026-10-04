# Input schemas and validation

Input components use `Request.<model>` names, separate from response components. The generator reads the same request-property metadata as `HttpClassValidator`, including input aliases, promoted defaults and optional rules. Response naming remains independent.

```php
use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Http\Validate\Attribute\Property;
use Raxos\Http\Validate\Constraint\MaxLength;
use Raxos\OpenAPI\Attribute\Property as SchemaProperty;

final readonly class CreateArticle implements HttpRequestModelInterface
{
    public function __construct(
        #[Property(alias: 'display_name')]
        #[MaxLength(80)]
        #[SchemaProperty(alias: 'displayName')]
        public string $name,
        #[Property(optional: true)]
        public ?string $summary = null
    )
    {
    }
}
```

The request accepts `display_name`; the response schema can use `displayName`. Required input rejects missing, null and blank values even when the PHP property is nullable. An optional property still requires a value when it has neither a usable default nor a nullable type. Explicit property or class schemas take precedence over inference.

Supported constraints include min/max length, numeric bounds, choices, enums, nested request models and arrays, model identifiers, email, URL and date/time formats. Defaults remain available for optional input. Query parameters inferred from `MapQuery` preserve aliases, scalar/array types, enums and parameter defaults; an explicit query schema wins.

An optional Closure can depend on runtime context. Schema generation does not execute it. Use `#[Required(true)]` or `#[Required(false)]` from `Raxos\OpenAPI\Attribute` to specify a static contract. Unresolved rules produce diagnostics and `x-raxos-conditional-required`. Unmapped constraints produce `x-raxos-runtime-constraints`; supply an explicit schema for an exact representation.

JSON Schema formats and length constraints do not reproduce every runtime rule, such as rejection of whitespace-only required strings or custom PHP predicates. Validate requests at runtime even when generated clients validate their schemas.

`CursorPage` responses describe `items`, `next_cursor` and `has_more`, with no total count.
