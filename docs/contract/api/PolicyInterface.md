---
outline: deep
---

# PolicyInterface

`Raxos\Contract\Search\PolicyInterface` is an extension point for [raxos/search](/search/). Implement it to shape or restrict a search query before it runs, for example to scope results to the current tenant.

## Signature

```php
interface PolicyInterface
{
    public function apply(StructureInterface $structure, QueryInterface $query, MapInterface $context): PolicyDecision;
}
```

## Methods

### `apply(StructureInterface $structure, QueryInterface $query, MapInterface $context): PolicyDecision`

Applies the policy to the given query. `$structure` describes the ORM model being searched, `$query` is the query builder instance the policy may add conditions to, and `$context` carries caller supplied values such as the current tenant. Returns a `PolicyDecision` that allows or denies the search. May throw a `DatabaseExceptionInterface` or `SearchExceptionInterface`.

## Notes

- The `StructureInterface` and `QueryInterface` types come from `Raxos\Contract\Database\Orm` and `Raxos\Contract\Database\Query`, described by [raxos/database](/database/).
- `MapInterface` comes from `Raxos\Contract\Collection`, described by [raxos/collection](/collection/).
- `PolicyDecision` is a concrete value object from raxos/search, built with its `allow()`, `deny(string $reason)` and `denySilent(string $reason)` factory methods.
- This is a typical extension point: raxos/search calls into it, your application supplies the implementation. See [extension points](/contract/extension-points).

## Example

```php
<?php
declare(strict_types=1);

namespace App\Search\Policy;

use Override;
use Raxos\Contract\Collection\MapInterface;
use Raxos\Contract\Database\Orm\StructureInterface;
use Raxos\Contract\Database\Query\QueryInterface;
use Raxos\Contract\Search\PolicyInterface;
use Raxos\Search\Policy\PolicyDecision;

final readonly class MerchantSearchPolicy implements PolicyInterface
{
    #[Override]
    public function apply(StructureInterface $structure, QueryInterface $query, MapInterface $context): PolicyDecision
    {
        if (!$context->has('merchant_id')) {
            return PolicyDecision::deny('It is not possible to search outside the scope of a merchant.');
        }

        $query->where($structure->getColumn('merchant_id'), $context->get('merchant_id'));

        return PolicyDecision::allow();
    }
}
```
