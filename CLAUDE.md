# Raxos — CLAUDE.md

## Project overzicht

Raxos is een **PHP monorepo** met meerdere zelfstandige bibliotheken die Bas Milius gebruikt in persoonlijke projecten. Vereist minimaal **PHP 8.5**.

De repo is opgebouwd als een **git repository met submodules**. Elke submodule (`barcode`, `cache`, etc.) is een eigen git repository op GitHub onder `github.com/basmilius/raxos-<naam>`.

---

## Submodules en hun doel

| Map           | Namespace           | Doel                                                      |
|---------------|---------------------|-----------------------------------------------------------|
| `barcode`     | `Raxos\Barcode\`    | Barcode-generators (QR, PDF417, etc.) via GD              |
| `cache`       | `Raxos\Cache\`      | Redis-gebaseerde cache (RedisCache, RedisTaggedCache)     |
| `collection`  | `Raxos\Collection\` | Collection-primitieven: ArrayList, Map, Paginated, etc.   |
| `container`   | `Raxos\Container\`  | Dependency-injection container met PHP attributes         |
| `contract`    | `Raxos\Contract\`   | Interfaces/contracts voor alle andere modules             |
| `database`    | `Raxos\Database\`   | ORM en Query Builder (PDO, MySQL/MariaDB/SQLite)          |
| `datetime`    | `Raxos\DateTime\`   | Datum/tijd utilities                                      |
| `error`       | `Raxos\Error\`      | Basis exception-klassen met ExceptionId                   |
| `foundation`  | `Raxos\Foundation\` | Basis-utilities (Access traits, IP, Option, Util-helpers) |
| `http`        | `Raxos\Http\`       | HttpRequest, HttpResponse, HttpMethod, etc.               |
| `mail`        | `Raxos\Mail\`       | Mail-verzending via Mailgun, Postmark, SMTP               |
| `message-bus` | `Raxos\MessageBus\` | Message bus met queue-ondersteuning                       |
| `oauth2`      | `Raxos\OAuth2\`     | OAuth2-server integratie voor raxos/router                |
| `openapi`     | `Raxos\OpenAPI\`    | OpenAPI 3.1.1 spec-generator via PHP attributes           |
| `rate-limit`  | `Raxos\RateLimit\`  | Rate limiting voor raxos/router (Redis-backed)            |
| `reflection`  | `Raxos\Reflection\` | PHP reflectie-wrappers (Class/Method/Property/Type)       |
| `router`      | `Raxos\Router\`     | Attribuut-gebaseerde router                               |
| `search`      | `Raxos\Search\`     | Zoekprovider (gebouwd op raxos/database)                  |
| `security`    | `Raxos\Security\`   | JWT, HMAC, NanoId, ULID, TOTP/2FA, tokens                 |
| `terminal`    | `Raxos\Terminal\`   | CLI-framework met commands, middleware, printer           |
| `wallet`      | `Raxos\Wallet\`     | Apple Wallet pass-generator                               |

---

## PHP-stijlconventies

### Bestandsstructuur

```php
<?php
declare(strict_types=1);

namespace Raxos\<Module>\<Sub>;

use Raxos\Contract\SomeInterface;
use function array_map;
use const JSON_THROW_ON_ERROR;
```

- `declare(strict_types=1)` staat altijd direct na `<?php`, zonder lege regel ertussen
- Imports zijn alfabetisch gesorteerd
- `use function` en `use const` komen na class-imports, ook alfabetisch
- Geen `use Foo\{Bar\ClassA, Baz\ClassC}` — namespaces worden nooit gemengd in een groep

### Klassen

- Geef de voorkeur aan `final readonly class`
- `readonly class` als er geen overerving nodig is maar wel uitbreidbaar moet zijn
- `abstract class` alleen als het echt abstract is
- Klasse-accolades op de **volgende regel** (`next_line` stijl)
- Methode-accolades ook op de **volgende regel**

### PHPDoc — verplicht op elke klasse, methode en property

```php
/**
 * Class Foo
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\<Module>
 * @since x.x.x
 */
```

```php
/**
 * Does something useful.
 *
 * @param string $value
 * @return string
 * @author Bas Milius <bas@mili.us>
 * @since x.x.x
 */
```

- `@author` is altijd `Bas Milius <bas@mili.us>`
- `@since` bevat de versie van de module (niet de datum)
- Bij nieuwe parameters ook de PHPDoc bijwerken
- `@throws` toevoegen als de methode een exception kan gooien

### Functions en parameters

- Arrow functions (`fn()`) voor simpele callbacks
- `static` modifier gebruiken als `$this` niet nodig is
- Typed parameters zijn verplicht — nooit untyped
- Constructor property promotion is de standaard

```php
// Correct
public function __construct(
    public readonly string $name,
    public readonly ?int $age = null,
) {}

// Arrow function
$result = array_map(static fn(string $item): string => strtolower($item), $items);
```

### Curly braces

- Altijd curly braces, ook voor simpele `if`/`for` statements (zie editorconfig: `if_brace_force = always`)

---

## Foutafhandeling (raxos/error)

Alle exceptions erven van `Raxos\Error\Exception`:

```php
final class SomethingFailedException extends Exception
{
    public function __construct(string $detail, ?Throwable $previous = null)
    {
        parent::__construct(
            error: 'something_failed',
            errorDescription: $detail,
            previous: $previous,
        );
    }
}
```

- `ExceptionId::for(static::class)` wordt automatisch gegenereerd als `$code` null is
- Exceptions implementeren doorgaans ook een interface uit `raxos/contract`

---

## ORM (raxos/database)

Models erven van `Raxos\Database\Orm\Model` en gebruiken PHP attributes:

```php
#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public int $id;

    #[Column]
    public string $name;

    #[HasMany(Address::class)]
    public ModelArrayList $addresses;

    #[BelongsTo]
    public Role $role;
}
```

Beschikbare ORM-attributes: `#[Table]`, `#[PrimaryKey]`, `#[Column]`, `#[Alias]`, `#[Computed]`,
`#[Caster]`, `#[Hidden]`, `#[Visible]`, `#[Immutable]`, `#[SoftDelete]`, `#[OnDuplicateUpdate]`,
`#[HasOne]`, `#[HasMany]`, `#[HasOneThrough]`, `#[HasManyThrough]`, `#[BelongsTo]`, `#[BelongsToMany]`, `#[BelongsToThrough]`, `#[Polymorphic]`, `#[Macro]`.

---

## Router (raxos/router)

Controllers gebruiken PHP attributes voor routing:

```php
#[Controller('/api/users')]
final class UserController
{
    #[Get('/')]
    public function index(HttpRequest $request): array { ... }

    #[Get('/{id}')]
    public function show(HttpRequest $request, int $id): User { ... }

    #[Post('/')]
    #[Validated]
    public function store(HttpRequest $request, #[MapModel] CreateUserRequest $body): User { ... }
}
```

Route-attributes: `#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`, `#[Head]`, `#[Options]`, `#[Any]`
Overige: `#[Controller]`, `#[Child]`, `#[Injected]`, `#[MapQuery]`, `#[MapModel]`, `#[MapHeader]`, `#[MapModelRelation]`, `#[Validated]`

Router aanmaken:

```php
$router = Router::createFromControllers($container, [UserController::class]);
```

---

## Container (raxos/container)

```php
$container = new Container(production: true);
$container->singleton(DatabaseConnection::class, fn() => new DatabaseConnection(...));
$container->bind(SomeInterface::class, SomeImpl::class);

$instance = $container->make(SomeService::class);
```

Attributes voor auto-wiring: `#[Singleton]`, `#[Inject]`, `#[Proxy]`, `#[Tag]`

---

## Composer per submodule

Elke submodule heeft een eigen `composer.json`. Cross-module dependencies worden via path repositories opgegeven:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../*",
            "symlink": true
        }
    ],
    "require": {
        "raxos/foundation": "*"
    }
}
```

`minimum-stability` is altijd `dev`, `prefer-stable: true`. Platform is altijd `php: 8.5`.

---

## Geen tests

Er zijn geen Pest- of PHPUnit-tests aanwezig in dit project. Schrijf dus geen tests tenzij expliciet gevraagd.

---

## Werken met submodules

Elke map is een zelfstandige git repo. Wijzigingen in een submodule committen en pushen doe je vanuit die map:

```bash
cd barcode
git add src/...
git commit -m "feat: ..."
```

De root-repo trackt de submodules via `.gitmodules`. Na wijzigingen in een submodule de pointer bijwerken in de root:

```bash
git add barcode
git commit -m "chore: bump barcode"
```

---

## EditorConfig (geldt voor alle submodules)

- Indentatie: **4 spaties** (geen tabs)
- Einde van regel: **LF**
- Encoding: **UTF-8**
- Lege regel aan het einde van bestanden
- Max regellengte: 999 (praktisch geen limiet)
