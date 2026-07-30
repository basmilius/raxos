---
outline: deep
---

# Read-only models

A `Model` carries its full ORM surface: `save()`, `destroy()`, the static `update()` and `delete()`, and a `__call()` that turns any relation name into a query. That is exactly what you want in application code, and exactly what you do not want to hand to a template engine.

Template engines resolve <code v-pre>{{ item.name }}</code> by first looking for a property and then falling back to a method. On a model that means a template author can write <code v-pre>{{ item.destroy }}</code> and delete the record while the page renders. The template does not look like code, but it is.

`ReadonlyModel` is the answer: a read-only view on a model that shares its `Backbone`, and therefore its data, its caches and its loaded relations, but has no way to change anything.

```php
$user = User::singleOrFail('usr_1');

$twig->render('profile.twig', [
    'user' => $user->readonly()
]);
```

## What a read-only model can and cannot do

A `ReadonlyModel` reads: columns, macros, embedded values and relations, by name or by alias, through property access, array access and `toArray()`. The visibility methods work too, so `only()`, `makeHidden()` and `makeVisible()` behave exactly as they do on a `Model`.

It cannot write. There is no `save()`, no `destroy()` and no `Queryable`, so those methods simply do not exist. Writing a property, unsetting one or calling any method throws a `ReadonlyModelException`.

```php
$readonly = $user->readonly();

$readonly->name;                  // reads
$readonly['name'];                // reads
$readonly->only(['id', 'name']);  // returns another ReadonlyModel

$readonly->name = 'Bas';          // ReadonlyModelException
$readonly->destroy();             // ReadonlyModelException
$readonly->nope;                  // MissingPropertyException
```

Relations are wrapped on the way out, so a read-only model never hands you a writable one:

```php
$readonly->role;         // ReadonlyModel
$readonly->role->users;  // ModelArrayList of ReadonlyModel
```

## The shared backbone

`readonly()` does not copy anything. The view is a second instance over the same `Backbone`, the same way `makeHidden()` and `only()` already returned a second instance over the same backbone.

That has two useful consequences. Reading a relation through the read-only view fills the shared relation cache, so the writable model does not query again. And a change made through the writable model is visible through the view immediately, because there is only one set of data.

```php
$user = User::singleOrFail('usr_1');
$readonly = $user->readonly();

$user->name = 'Bas';

$readonly->name;                     // 'Bas'
$readonly->backbone === $user->backbone;  // true
```

What it does not do is guard against read side effects. Reading a relation still triggers lazy loading and reading a macro still runs its closure, identical to reading them on the model itself. A read-only model guards against mutation, not against queries.

## Collections

`ModelArrayList::readonly()` does the same for a whole collection:

```php
$users = User::all()->readonly();

$users[0];  // ReadonlyModel
```

## Typing against a model

Both classes implement `Raxos\Contract\Database\Orm\ModelInterface`, which describes the read surface: value access, `toArray()`, `jsonSerialize()` and the visibility methods. `Model` additionally implements `MutableModelInterface`, which adds `save()`, `destroy()` and the setters.

Type hint against `ModelInterface` in code that only reads, and against `Model` or `MutableModelInterface` in code that writes.

```php
function renderCard(ModelInterface $model): string
{
    return $twig->render('card.twig', ['item' => $model->readonly()]);
}
```

`readonly()` is available on both, and on a `ReadonlyModel` it returns the same instance, so calling it twice is free and always safe.

See [Model](/database/api/Model) and [ReadonlyModel](/database/api/ReadonlyModel) for the full method lists.
