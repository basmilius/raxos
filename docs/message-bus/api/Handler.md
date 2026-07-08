---
outline: deep
---

# Handler

`Raxos\MessageBus\Attribute\Handler` is a class level attribute that links a message class to the handler that processes it. It is the one piece of wiring the bus needs: at consume time the queue reads this attribute from the message class through reflection to find the right handler.

```php
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Handler
```

## Constructor

```php
public function __construct(
    string $handlerClass
)
```

Takes a single class-string pointing at a `Raxos\Contract\MessageBus\HandlerInterface` implementation. The value is stored on the public `handlerClass` property.

## Usage

Apply the attribute once, at the class level, on the message.

```php
<?php
declare(strict_types=1);

namespace App\MessageBus;

use Raxos\Contract\MessageBus\MessageInterface;
use Raxos\MessageBus\Attribute\Handler;

#[Handler(SendWelcomeEmailMessageHandler::class)]
final readonly class SendWelcomeEmailMessage implements MessageInterface
{
    public function __construct(
        public string $userId
    ) {}

    public function __serialize(): array
    {
        return [$this->userId];
    }

    public function __unserialize(array $data): void
    {
        $this->userId = $data[0];
    }
}
```

If a message is consumed without a `Handler` attribute, `MessageBusQueue::consume()` throws a `MessageBusMissingHandlerException`.

## See also

- [Messages and handlers](/message-bus/messages-and-handlers): defining messages and handlers.
- [MessageBusQueue](/message-bus/api/MessageBusQueue): reads this attribute during `consume()`.
