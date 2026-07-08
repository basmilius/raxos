---
outline: deep
---

# MutationListenerInterface

`Raxos\Contract\Database\Orm\MutationListenerInterface` is an extension point for [raxos/database](/database/). Implement it on a model to react whenever one of its tracked properties changes.

## Signature

```php
interface MutationListenerInterface
{
    public function onMutation(PropertyDefinition $property, mixed $newValue, mixed $oldValue): void;
}
```

## Methods

### `onMutation(PropertyDefinition $property, mixed $newValue, mixed $oldValue): void`

Invoked when a mutation happens on a property that is tracked by the model. `$property` is the `PropertyDefinition` describing which column changed, `$newValue` is the value being assigned and `$oldValue` is the previous value.

## Notes

- Only mutations on properties tracked by the ORM (columns declared with `#[Column]`, `#[Alias]` and similar) trigger the callback, not arbitrary object properties.
- `PropertyDefinition` comes from raxos/database's `Raxos\Database\Orm\Definition` namespace.
- A model implements this interface directly alongside its own `#[Table]`/`#[Column]` attributes, so the listener lives on the model itself rather than in a separate class.
- This is a typical extension point: raxos/database calls into it, your model supplies the implementation. See [extension points](/contract/extension-points).

## Example

```php
<?php
declare(strict_types=1);

namespace App\Models;

use Override;
use Raxos\Contract\Database\Orm\MutationListenerInterface;
use Raxos\Database\Orm\Definition\PropertyDefinition;
use Raxos\Database\Orm\Model;

final class Transaction extends Model implements MutationListenerInterface
{
    #[Override]
    public function onMutation(PropertyDefinition $property, mixed $newValue, mixed $oldValue): void
    {
        if ($property->name === 'status') {
            // ... react to the status change, for example logging or dispatching an event.
        }
    }
}
```
