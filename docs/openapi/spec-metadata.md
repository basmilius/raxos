---
outline: deep
---

# Document metadata and security

[RouterBuilder](/openapi/api/RouterBuilder) produces the operations and schemas, but the surrounding document (its title, servers, tags and security schemes) is assembled by hand when you construct the [OpenAPI](/openapi/api/OpenAPI) instance. All of these live in the `Raxos\OpenAPI\Definition` namespace.

## Info

`Info` carries the title, version, and optional summary, description, terms of service, contact and license shown at the top of the document. Only `title` and `version` are required.

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\Definition\{Contact, Info, License};

$info = new Info(
    title: 'My API',
    version: '1.0.0',
    summary: 'Public API of my application.',
    description: 'A longer description in CommonMark.',
    contact: new Contact('API Team', email: 'api@example.com'),
    license: new License('MIT', url: 'https://opensource.org/licenses/MIT')
);
```

## Servers

`Server` describes a base URL. Add one per environment.

```php
use Raxos\OpenAPI\Definition\Server;

$servers = [
    new Server('https://api.example.com', 'Production'),
    new Server('https://staging.example.com', 'Staging'),
    new Server('http://localhost:8080', 'Development')
];
```

A single templated `Server` can replace several fixed ones by declaring `variables`. Each variable name in the URL (written as `{name}`) needs a matching `ServerVariable` with a default value and, optionally, the list of allowed values.

```php
use Raxos\OpenAPI\Definition\{Server, ServerVariable};

$servers = [
    new Server('https://{environment}.example.com', variables: [
        'environment' => new ServerVariable('api', enum: ['api', 'staging'])
    ])
];
```

## Tags

`Tag` groups operations in the generated document. Use the same tag names here that you pass to the `tags` argument of `#[Endpoint]`.

```php
use Raxos\OpenAPI\Definition\Tag;

$tags = [
    new Tag('Me', 'Operations on the current user.'),
    new Tag('Products', 'Browse and manage products.')
];
```

## Components and security schemes

`Components` bundles the reusable responses and schemas collected by `RouterBuilder`, plus any security schemes you define. A `SecurityScheme` documents one authentication method, and the `security` argument on `#[Endpoint]` references it by name.

```php
use Raxos\OpenAPI\Definition\{Components, SecurityScheme};
use Raxos\OpenAPI\Enum\{SecuritySchemeType, SecurityType};

$components = new Components(
    responses: $builder->responses->toArray(),
    schemas: $builder->schemas->toArray(),
    securitySchemes: [
        'bearer' => new SecurityScheme(
            type: SecurityType::HTTP,
            scheme: SecuritySchemeType::BEARER,
            bearerFormat: 'JWT'
        )
    ]
);
```

`SecurityType` covers `apiKey`, `http`, `oauth2` and `openIdConnect`. `SecuritySchemeType` covers `basic` and `bearer`. For an API key scheme, set `type: SecurityType::API_KEY` together with an `in` (from the `In` enum) and a `name`.

## Putting it together

```php
<?php
declare(strict_types=1);

use Raxos\OpenAPI\OpenAPI;

$openapi = new OpenAPI(
    info: $info,
    servers: $servers,
    paths: $builder->paths->toArray(),
    components: $components,
    tags: $tags
);

echo $openapi->getYAML();
```

::: info
The security requirement on `#[Endpoint(security: ['bearer'])]` is a plain array of scheme names, each resolving to a scheme key defined in `Components::$securitySchemes`. See [Documenting endpoints](/openapi/documenting-endpoints) for the endpoint side.
:::
