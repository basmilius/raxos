---
outline: deep
---

# Policies

A policy guards a model before any filter runs. It can scope a search to the current tenant or user, allow it to continue, quietly drop the model from the results, or reject the whole search with an error. You attach policies to a model with the repeatable `#[Policy]` attribute.

## Declaring a policy

```php
use Raxos\Search\Attribute\Policy;

#[Policy(new TenantScopedPolicy())]
final class Article extends Model { /* ... */ }
```

A model may carry several `#[Policy]` attributes. Each one runs in order before the model's filters are applied.

## The policy contract

A policy implements `PolicyInterface`:

```php
public function apply(
    StructureInterface $structure,
    QueryInterface $query,
    MapInterface $context
): PolicyDecision;
```

The `$context` is the map passed to `SearchProvider::search()`. It is typically used to scope a query to the current tenant or user. The method returns a [`PolicyDecision`](/search/api/Policy), which carries one of three verdicts.

## Decisions

- `PolicyDecision::allow()` lets the search proceed for this model.
- `PolicyDecision::denySilent($reason)` drops just this model from the results, with no error raised.
- `PolicyDecision::deny($reason)` stops the whole search by throwing `IllegalSearchException` with the given reason.

`IllegalSearchException` extends the base exception from [raxos/error](/error/) and exposes the originating decision.

## A scoping policy

A common pattern is to add a `where()` clause of your own so every search is limited to the current tenant, then allow the search to continue:

```php
<?php
declare(strict_types=1);

namespace App\Search;

use Raxos\Contract\Collection\MapInterface;
use Raxos\Contract\Database\Orm\StructureInterface;
use Raxos\Contract\Database\Query\QueryInterface;
use Raxos\Contract\Search\PolicyInterface;
use Raxos\Search\Policy\PolicyDecision;

final readonly class TenantScopedPolicy implements PolicyInterface
{
    public function apply(StructureInterface $structure, QueryInterface $query, MapInterface $context): PolicyDecision
    {
        if (!$context->has('tenant_id')) {
            return PolicyDecision::deny('Missing tenant context.');
        }

        $query->where($structure->class::col('tenant_id'), $context->get('tenant_id'));

        return PolicyDecision::allow();
    }
}
```

Here a missing tenant rejects the whole search, while a present tenant scopes the query and lets it run. To hide a model without surfacing an error (for example when the current user simply has no access to that type), return `PolicyDecision::denySilent()` instead.
