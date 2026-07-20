---
outline: deep
---

# Primer

A primer resolves a value for a whole batch of freshly hydrated models in a single query and seeds it into each model's macro cache, so reading a computed `#[Macro]` property while serializing a list no longer runs one query per model. Primers are registered per query with [`Query::prime()`](/database/api/Query) and are opt-in, so paths that must read live data simply omit them.

## PrimerInterface

`Raxos\Contract\Database\Orm\PrimerInterface`

```php
interface PrimerInterface
{
    public function prime(ArrayListInterface $instances, ConnectionInterface $connection): void;
}
```

| Method | Description |
| --- | --- |
| `prime(ArrayListInterface $instances, ConnectionInterface $connection): void` | Runs one bulk query on the given connection and seeds each model's macro cache. |

A primer may also be a plain callable with the same signature.

## PrimerTiming

`Raxos\Contract\Database\Orm\PrimerTiming` controls when a primer runs relative to the eager loading of relations.

| Case | Runs |
| --- | --- |
| `PrimerTiming::BeforeRelations` | After the models are hydrated, before relations are eager loaded. |
| `PrimerTiming::AfterRelations` | After relations have been eager loaded (the default). Use this when the primer reads a loaded relation. |

## Registering

```php
public function prime(PrimerInterface|callable $primer, PrimerTiming $timing = PrimerTiming::AfterRelations): static
```

Multiple primers may be registered; primers with the same timing run in registration order. Priming runs once over the whole batch produced by `array()`, `arrayList()`, `single()` or `paginate()`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\Collection\ArrayListInterface;
use Raxos\Contract\Database\ConnectionInterface;
use Raxos\Contract\Database\Orm\PrimerInterface;
use Raxos\Database\Query\Expr;

final class UserPostCountPrimer implements PrimerInterface
{
    public function prime(ArrayListInterface $instances, ConnectionInterface $connection): void
    {
        $rows = $connection->query()
            ->select(['user_id', 'total' => Expr::count()])
            ->from('post')
            ->whereIn('user_id', $instances->column('id')->toArray())
            ->groupBy('user_id')
            ->array();

        $byUser = array_column($rows, 'total', 'user_id');

        foreach ($instances as $user) {
            $user->backbone->macroCache->setValue('postCount', $byUser[$user->id] ?? 0);
        }
    }
}

$users = User::select()
    ->prime(new UserPostCountPrimer())
    ->arrayList();
```
