---
outline: deep
---

# Policy

The pieces that let a policy allow, deny or silently drop a search. See the [policies concept](/search/policies) for how to attach and write a policy.

## #[Policy]

`Raxos\Search\Attribute\Policy`

A repeatable class attribute that registers a policy on a model. The policy is evaluated before any filter runs.

```php
public function __construct(
    public PolicyInterface $policy
)
```

```php
use Raxos\Search\Attribute\Policy;

#[Policy(new TenantScopedPolicy())]
final class Article extends Model { /* ... */ }
```

## PolicyDecision

`Raxos\Search\Policy\PolicyDecision`

The value a policy's `apply()` method returns. It carries a verdict and an optional reason, and is a final readonly class with a private constructor: you create one through the static factories.

### Static factories

```php
public static function allow(): self
```

Lets the search proceed for this model.

```php
public static function deny(string $reason): self
```

Stops the whole search by throwing `IllegalSearchException` with the given reason.

```php
public static function denySilent(string $reason): self
```

Drops just this model from the results, with no exception thrown.

### Properties

- `PolicyVerdict $verdict`: the chosen verdict.
- `?string $reason`: the reason passed to `deny()` or `denySilent()`, if any.

## PolicyVerdict

`Raxos\Search\Enum\PolicyVerdict`

A pure enum with the cases `ALLOW`, `DENY` and `DENY_SILENT`, used internally by `PolicyDecision` and read by `SearchProvider` to decide how to handle a model.

## Usage

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

A `deny()` verdict surfaces as `IllegalSearchException`, which extends the base exception from [raxos/error](/error/).

## Related

- [Policies](/search/policies): the concept page with the full flow.
- [SearchProvider](/search/api/SearchProvider): where the context map is passed in.
