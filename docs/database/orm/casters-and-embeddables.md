---
outline: deep
---

# Casters, embeddables and polymorphic models

Three more attribute families shape how column values map to PHP: casters convert a single value, embeddables fold a value object into the table, and polymorphic models share one table across subclasses. Macros add computed virtual properties on top.

## Casters

A caster converts a column value in both directions: `decode()` turns the stored value into its PHP form when reading, `encode()` turns the PHP value back into a storable value when writing. Assign one with `#[Caster]`, passing the caster class.

The package ships five casters:

| Caster | Converts |
| --- | --- |
| `BooleanCaster` | A `0` / `1` column to and from a PHP `bool`. |
| `FloatCaster` | A numeric column to and from a PHP `float`. |
| `JsonCaster` | A JSON text column to and from an array or object. |
| `IntSetCaster` | A delimited column to and from a list of integers. |
| `StringSetCaster` | A delimited column to and from a list of strings. |

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Caster, Column, PrimaryKey, Table};
use Raxos\Database\Orm\Caster\{BooleanCaster, JsonCaster};

#[Table('products')]
final class Product extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Caster(BooleanCaster::class)]
    #[Column]
    public bool $isActive;

    #[Caster(JsonCaster::class)]
    #[Column]
    public array $metadata;
}
```

You can write your own caster by implementing `Raxos\Contract\Database\Orm\CasterInterface`, which declares the `decode()` and `encode()` methods, and reference it from `#[Caster]` the same way.

## Embeddables

An embeddable is a value object that lives inside a model's table rather than in a table of its own. Mark the value object class with `#[Embeddable]`, then mount it on a model property with `#[Embedded]`. A column prefix keeps the underlying columns namespaced.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, Embeddable, Embedded, PrimaryKey, Table};

#[Embeddable]
final class Address
{
    #[Column]
    public string $street;

    #[Column]
    public string $city;

    #[Column]
    public string $postalCode;
}

#[Table('customers')]
final class Customer extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Embedded(prefix: 'billing_')]
    public Address $billing;
}
```

Here the `Customer` table stores `billing_street`, `billing_city` and `billing_postal_code`, and `toArray()` nests the address back under the `billing` key.

## Polymorphic models

`#[Polymorphic]` maps a discriminator column to concrete model subclasses that all share one table (single table inheritance). The column defaults to `type`, and the map pairs each discriminator value with a class.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, Polymorphic, PrimaryKey, Table};

#[Table('media')]
#[Polymorphic('kind', [
    'image' => Image::class,
    'video' => Video::class,
])]
abstract class Media extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $kind;
}

final class Image extends Media {}
final class Video extends Media {}
```

Loading a `media` row hydrates it as an `Image` or a `Video` depending on the value of the `kind` column.

## Macros

`#[Macro]` defines a computed virtual property backed by a callback. The value is cached per instance by default, so the callback runs once. Pass `isCached: false` to recompute on every access.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, Macro, PrimaryKey, Table};

#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $firstName;

    #[Column]
    public string $lastName;

    #[Macro(static fn(User $user): string => "{$user->firstName} {$user->lastName}")]
    public string $fullName;
}
```

Like columns, a macro can be hidden from export with `#[Hidden]` and given an export key with `#[Alias]`. See [models](/database/orm/models) for the visibility rules and the [ORM attributes reference](/database/api/Attributes) for every attribute.
