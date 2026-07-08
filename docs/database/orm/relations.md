---
outline: deep
---

# Relations

Relations link one model to another through attributes on a property. The ORM resolves the property to a query, loads the related records and assigns them to the property.

## The relation attributes

| Attribute | Describes |
| --- | --- |
| `#[HasOne]` | A one to one relation where the other model holds the foreign key. |
| `#[HasMany]` | A one to many relation where the other model holds the foreign key. |
| `#[BelongsTo]` | The inverse of a has one or has many; this model holds the foreign key. |
| `#[BelongsToMany]` | A many to many relation through a linking table. |
| `#[HasOneThrough]` | A one to one relation reached through an intermediate model. |
| `#[HasManyThrough]` | A one to many relation reached through an intermediate model. |
| `#[BelongsToThrough]` | The inverse of a through relation. |

For `#[HasOne]` and `#[BelongsTo]`, the related model is inferred from the property type. For `#[HasMany]` (and the through and many to many attributes) the property type is a `ModelArrayList`, so the related model is passed explicitly.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\{Model, ModelArrayList};
use Raxos\Database\Orm\Attribute\{BelongsTo, Column, HasMany, HasOne, PrimaryKey, Table};

#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[HasOne]
    public Profile $profile;

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

Accessing the property loads the relation on first read and assigns it:

```php
$user = User::singleOrFail('usr_1');

$profile = $user->profile;   // Profile
$posts = $user->posts;       // ModelArrayList of Post
```

## Many to many

`#[BelongsToMany]` connects two models through a linking table. Pass the related model, and optionally the linking table and key names when they cannot be inferred from convention.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\{Model, ModelArrayList};
use Raxos\Database\Orm\Attribute\{BelongsToMany, Column, PrimaryKey, Table};

#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[BelongsToMany(Role::class, linkingTable: 'user_roles')]
    public ModelArrayList $roles;
}
```

## Through relations

The through attributes cross an intermediate model. `#[HasOneThrough]` and `#[BelongsToThrough]` take the linking model, `#[HasManyThrough]` takes both the reference model and the linking model.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\{Model, ModelArrayList};
use Raxos\Database\Orm\Attribute\{Column, HasManyThrough, PrimaryKey, Table};

#[Table('countries')]
final class Country extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    // Posts written by users who live in this country.
    #[HasManyThrough(Post::class, User::class)]
    public ModelArrayList $posts;
}
```

Every relation attribute accepts explicit key overrides (`referenceKey`, `declaringKey` and their table variants) for when the column names do not follow convention.

## Eager loading

By default a relation loads lazily on first access, which can trigger an extra query per parent record. To avoid that, eager load the relation so it is fetched in one batch for the whole result set.

Set `eagerLoad: true` on the attribute to always eager load it:

```php
#[HasMany(Post::class, eagerLoad: true)]
public ModelArrayList $posts;
```

Or control it per query with `eagerLoad()` and `eagerLoadDisable()`:

```php
<?php
declare(strict_types=1);

$users = User::select()
    ->eagerLoad('posts')
    ->arrayList();

$lean = User::select()
    ->eagerLoadDisable('posts')
    ->arrayList();
```

## Filtering on relations

`whereHas()` adds a `where exists` condition based on a relation, optionally refined by a callback. `whereRelation()` is a shorthand that adds a comparison inside that relation. Both have `orWhereHas()` / `orWhereRelation()` and negated `whereNotHas()` variants.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Database\Query\QueryInterface;

// Users that have at least one published post.
$authors = User::select()
    ->whereHas('posts', static fn(QueryInterface $query) => $query
        ->where('is_published', 1))
    ->arrayList();

// Users whose posts have a given status.
$authors = User::select()
    ->whereRelation('posts', 'status', 'published')
    ->arrayList();
```

## Relation queries

Calling a relation as a method returns the underlying query instead of the loaded records, so you can refine it before executing.

```php
<?php
declare(strict_types=1);

$user = User::singleOrFail('usr_1');

$recentPosts = $user->posts()
    ->where('created_on', '>', 1710000000)
    ->orderByDesc('created_on')
    ->arrayList();
```
