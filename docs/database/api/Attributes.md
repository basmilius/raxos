---
outline: deep
---

# ORM attributes

The attributes in `Raxos\Database\Orm\Attribute` describe how a model maps to a table: its columns and keys, its relations, value conversion, embeddables and single table inheritance. A class attribute targets the model class, a property attribute targets a mapped property.

## Structure

| Attribute | Target | Purpose |
| --- | --- | --- |
| `#[Table(string $name)]` | class | Declares the database table for a model. |
| `#[Column(?string $key = null)]` | property | Marks a property as a mapped column, optionally with a different column key. |
| `#[PrimaryKey]` | property | Marks a column property as (part of) the primary key. Extends `#[Column]`. |
| `#[ForeignKey]` | property | Marks a column property as a foreign key. Extends `#[Column]`. |
| `#[Alias(?string $alias = null)]` | property | Gives a property an export alias used by `toArray()` and `jsonSerialize()`. |

## Visibility and mutability

| Attribute | Target | Purpose |
| --- | --- | --- |
| `#[Hidden]` | property | Hides a column, macro or relation from export by default. |
| `#[Visible(array\|string\|null $only = null)]` | property | Forces a field visible by default, optionally restricted to a set of nested keys. |
| `#[Immutable]` | property | Blocks write access to the field after the model is hydrated. |
| `#[Computed]` | property | Marks a column as database computed, so it is excluded from insert and update. |

## Behavior

| Attribute | Target | Purpose |
| --- | --- | --- |
| `#[SoftDelete(string $column = 'deleted_on')]` | class | Enables soft deletes using the given timestamp column. |
| `#[OnDuplicateUpdate(array\|string $fields)]` | class | Lists fields to update automatically on an insert that hits a duplicate key. |
| `#[ConnectionId(string $connectionId = 'default')]` | class | Selects which connection, as registered with `Db::register()`, the model uses, so a table's model can target a non-default registered connection. |
| `#[Macro(Closure $callback, bool $isCached = true)]` | property | Defines a computed, by default cached, virtual property. |

## Value conversion and embeddables

| Attribute | Target | Purpose |
| --- | --- | --- |
| `#[Caster(string $casterClass)]` | property | Assigns a caster that converts a column value between database and PHP form. |
| `#[Embeddable]` | class | Marks a value object class as embeddable inside a model's table. |
| `#[Embedded(string $prefix = '')]` | property | Mounts an embeddable on a model property, with an optional column prefix. |
| `#[Polymorphic(string $column = 'type', array $map = [])]` | class | Maps a discriminator column to concrete model subclasses that share one table. |

## Relations

All relation attributes accept key overrides (`referenceKey`, `declaringKey` and their table variants), an `eagerLoad` flag and a `withDeleted` flag. The ones that map to a collection also take an `orderBy`.

| Attribute | Target | First arguments |
| --- | --- | --- |
| `#[HasOne]` | property | Key overrides only; the related model is inferred from the property type. |
| `#[HasMany(string $referenceModel, ...)]` | property | The related model class. |
| `#[BelongsTo]` | property | Key overrides only; the related model is inferred from the property type. |
| `#[BelongsToMany(string $referenceModel, ?string $linkingTable = null, ...)]` | property | The related model and optional linking table. |
| `#[HasOneThrough(string $linkingModel, ...)]` | property | The intermediate model. |
| `#[HasManyThrough(string $referenceModel, string $linkingModel, ...)]` | property | The related model and the intermediate model. |
| `#[BelongsToThrough(string $linkingModel, ...)]` | property | The intermediate model. |

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\{Model, ModelArrayList};
use Raxos\Database\Orm\Attribute\{
    Alias, BelongsTo, Caster, Column, HasMany, Hidden, PrimaryKey, SoftDelete, Table
};
use Raxos\Database\Orm\Caster\BooleanCaster;

#[Table('users')]
#[SoftDelete]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $name;

    #[Hidden]
    #[Column]
    public string $passwordHash;

    #[Caster(BooleanCaster::class)]
    #[Column]
    public bool $isActive;

    #[Alias('created_at')]
    #[Column]
    public int $createdOn;

    #[HasMany(Post::class)]
    public ModelArrayList $posts;
}

#[Table('posts')]
final class Post extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $userId;

    #[BelongsTo]
    public User $author;
}
```

For the concepts behind these attributes, see [models](/database/orm/models), [relations](/database/orm/relations) and [casters, embeddables and polymorphic models](/database/orm/casters-and-embeddables).
