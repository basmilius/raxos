---
outline: deep
---

# ModelArrayList

`Raxos\Database\Orm\ModelArrayList` is the collection type that every model query and every collection relation returns. It extends the collection package's `ArrayList` and implements `VisibilityInterface`, so on top of the general collection surface it can apply model visibility rules across every contained model at once.

```php
class ModelArrayList extends ArrayList implements VisibilityInterface
```

## Where it comes from

A `ModelArrayList` is returned by:

- `Model::all()` and `Model::find()`.
- The `arrayList()` terminal on a model bound query.
- Any property typed `ModelArrayList` for a `HasMany`, `BelongsToMany`, `HasManyThrough` or `HasOneThrough` relation.

## Visibility methods

Each method maps every contained model through the matching `Model` method and returns a new collection, leaving the originals untouched.

| Method | Description |
| --- | --- |
| `makeHidden(array\|string $keys): static` | Maps every model through `Model::makeHidden()`, hiding the given keys from export. |
| `makeVisible(array\|string $keys): static` | Maps every model through `Model::makeVisible()`, forcing the given keys visible. |
| `only(array\|string $keys): static` | Maps every model through `Model::only()`, exporting only the given keys. |

```php
<?php
declare(strict_types=1);

$users = User::all();

$public = $users->makeHidden('email');
$summary = $users->only(['id', 'name']);

$json = json_encode($summary);
```

## General collection methods

Because `ModelArrayList` extends `ArrayList`, every general purpose collection method applies too, including filtering, mapping, column extraction and the rest. See the [collection package](/collection/) for the full list.

```php
<?php
declare(strict_types=1);

$active = User::all()
    ->filter(static fn(User $user) => $user->isActive);

$names = User::all()
    ->column('name');
```

See [querying models](/database/orm/querying) for the methods that return a `ModelArrayList`, and [Model](/database/api/Model) for the per instance visibility methods it delegates to.
