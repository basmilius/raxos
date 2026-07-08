---
outline: deep
---

# MessageInterface

`Raxos\Contract\MessageBus\MessageInterface` is the contract every message dispatched through [raxos/message-bus](/message-bus/) implements, whether it is handled directly or placed on a queue.

## Signature

```php
interface MessageInterface extends SerializableInterface {}
```

The interface adds no members of its own. It exists purely as a marker on top of [`SerializableInterface`](/contract/api/SerializableInterface).

## Notes

- Extending `SerializableInterface` means a message must implement `__serialize()` and `__unserialize()`, so it can be put on a queue and restored later, even when it is dispatched directly and never actually queued.
- One [`HandlerInterface`](/contract/api/HandlerInterface) implementation is written per message type, matched by the concrete `MessageInterface` class the message implements.
- Messages are typically linked to their handler with raxos/message-bus's `#[Handler]` attribute.

## Example

```php
<?php
declare(strict_types=1);

namespace App\MessageBus\Message;

use Override;
use Raxos\Contract\MessageBus\MessageInterface;
use Raxos\DateTime\DateTime;

final readonly class SendWelcomeEmail implements MessageInterface
{
    public function __construct(
        public string $userId,
        public DateTime $requestedAt = new DateTime(),
    ) {}

    #[Override]
    public function __serialize(): array
    {
        return [$this->userId, $this->requestedAt->toIso8601String()];
    }

    #[Override]
    public function __unserialize(array $data): void
    {
        $this->userId = $data[0];
        $this->requestedAt = DateTime::parse($data[1]);
    }
}
```

See [`HandlerInterface`](/contract/api/HandlerInterface) for the matching handler contract.
