---
outline: deep
---

# ReadonlyModel

`Raxos\Database\Orm\ReadonlyModel` is a read-only view on a [Model](/database/api/Model). It shares the model's `Backbone`, so it reads the same data, caches and relations, but it exposes no mutation at all. It is safe to hand to untrusted consumers such as template engines.

Unlike `Model`, it is not a base class you extend. It is a single final class that wraps any backbone, and you get one by calling `readonly()` on a model or a collection.

```php
final class ReadonlyModel implements
    ArrayAccess,
    DebuggableInterface,
    ModelInterface
```

## Getting one

| Source | Result |
| --- | --- |
| `Model::readonly()` | A `ReadonlyModel` over the same backbone, carrying the model's visibility overrides. |
| `ModelArrayList::readonly()` | A `ModelArrayList` in which every model is replaced by its read-only view. |
| Reading a relation on a `ReadonlyModel` | A `ReadonlyModel` or a `ModelArrayList` of them. |
| `ReadonlyModel::readonly()` | The same instance. |

## Instance methods

| Method | Description |
| --- | --- |
| `getValue(string $key): mixed` | Reads a column, macro, embedded value or relation by name or alias. Related models come back read-only. |
| `hasValue(string $key): bool` | Returns `true` when the key maps to a property of the model. |
| `toArray(): array` | Converts the model to an array, honoring `#[Hidden]`, `#[Visible]` and any visibility overrides. |
| `jsonSerialize(): array` | Reuses `toArray()` for `json_encode()`. |
| `makeHidden(array\|string $keys): static` | Returns another read-only view with the given keys hidden from export. |
| `makeVisible(array\|string $keys): static` | Returns another read-only view with the given keys forced visible. |
| `only(array\|string $keys): static` | Returns another read-only view that exports only the given keys. |
| `readonly(): self` | Returns `$this`. |

Property access and array access map onto `getValue()` and `hasValue()`, so `$model->name`, `$model['name']` and `isset($model->name)` all work.

## What throws

| Attempt | Exception |
| --- | --- |
| `$model->name = 'Bas'`, `$model['name'] = 'Bas'`, `setValue()` | `ReadonlyModelException` |
| `unset($model->name)`, `unset($model['name'])`, `unsetValue()` | `ReadonlyModelException` |
| Any method call that is not in the table above | `ReadonlyModelException` |
| Reading a key that is not a property | `MissingPropertyException` |

The catch-all method call is deliberate. A template engine falls back to a method lookup when a name is not a property, and without it <code v-pre>{{ item.destroy }}</code> would render as an empty string instead of reporting the problem.

## Example

```php
<?php
declare(strict_types=1);

$user = User::singleOrFail('usr_1');
$readonly = $user->readonly();

$readonly->name;                          // reads
$readonly->role;                          // ReadonlyModel
$readonly->backbone === $user->backbone;  // true

$twig->render('profile.twig', [
    'user' => $readonly
]);
```

See [read-only models](/database/orm/readonly-models) for the narrative version, and [Model](/database/api/Model) for the writable side.
