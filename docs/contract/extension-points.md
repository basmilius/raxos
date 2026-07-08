---
outline: deep
---

# Extension points

Contract has two kinds of interfaces. Some, like [`ContainerInterface`](/contract/api/ContainerInterface) or `ConnectionInterface`, describe classes you consume: you type hint against them and let a Raxos package provide the implementation. Others are meant to be implemented by you, so you can plug custom behavior into a Raxos package. This page is about the second kind.

## Small interfaces you implement

Extension point interfaces are intentionally small, usually one or two methods, so implementing one is a quick way to hook into a package without subclassing its internals. Common examples:

- [`CasterInterface`](/contract/api/CasterInterface): custom ORM column types in [raxos/database](/database/).
- [`MiddlewareInterface`](/contract/api/MiddlewareInterface): pipeline steps in [raxos/router](/router/).
- [`HandlerInterface`](/contract/api/HandlerInterface): message consumers in [raxos/message-bus](/message-bus/).
- [`PolicyInterface`](/contract/api/PolicyInterface): query shaping in [raxos/search](/search/).
- [`MutationListenerInterface`](/contract/api/MutationListenerInterface): reacting to model property changes in [raxos/database](/database/).
- [`ConstraintAttributeInterface`](/contract/api/ConstraintAttributeInterface): custom validation constraints in [raxos/http](/http/).
- [`TransformerInterface`](/contract/api/TransformerInterface): reshaping a validated request value in [raxos/http](/http/).

## Why the interface lives here

Because the interface lives in Contract rather than in the package that consumes it, your implementation does not need to depend on that package's concrete classes at all. You depend on the small contract, the package depends on the same contract, and the two meet without being coupled.

The consuming package usually needs to know which of your classes to call. That link is made with an attribute you attach to your implementation or to the property that uses it, for example the `#[Caster]` attribute on a model column.

## Example: a custom ORM caster

`CasterInterface` is a typical extension point. raxos/database calls into it, your application supplies the implementation, and the `#[Caster]` attribute wires the two together.

```php
<?php
declare(strict_types=1);

namespace App\Casters;

use Raxos\Contract\Database\Orm\CasterInterface;
use Raxos\Database\Orm\Model;

final readonly class MoneyCaster implements CasterInterface
{
    public function decode(string|float|int|null $value, Model $instance): mixed
    {
        return $value === null ? null : (int) $value;
    }

    public function encode(mixed $value, Model $instance): string|float|int|null
    {
        return $value === null ? null : (string) $value;
    }
}
```

See the [CasterInterface reference](/contract/api/CasterInterface) for the full method contract, and the [extension point references](/contract/api/MiddlewareInterface) for the router and message bus equivalents.
