---
outline: deep
---

# Messages and handlers

A unit of work in Message Bus is split into two objects: a message that carries the data, and a handler that does the work. The two are linked by a class level `Handler` attribute, so the queue can find the right handler for any message it consumes without a separate routing table.

## Defining a message

A message is a plain PHP object that implements `Raxos\Contract\MessageBus\MessageInterface`. That interface extends `SerializableInterface`, which means every message must implement `__serialize()` and `__unserialize()`. The bus uses these to turn the object into a string when publishing and back into an object when consuming.

Mark the message class with the `Handler` attribute, passing the class-string of the handler that should process it.

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

::: tip
Keep the serialized payload small and made up of scalars or simple arrays. Store an identifier such as a user id rather than a whole entity, and let the handler load the current data when it runs.
:::

## Defining a handler

A handler implements `Raxos\Contract\MessageBus\HandlerInterface`, which has a single method:

```php
public function handle(MessageInterface $message, Printer $printer): void;
```

The `$printer` argument is a `Raxos\Terminal\Printer` from [raxos/terminal](/terminal/), useful for reporting progress while a worker runs.

```php
<?php
declare(strict_types=1);

namespace App\MessageBus;

use Raxos\Contract\MessageBus\{HandlerInterface, MessageInterface};
use Raxos\Terminal\Printer;

final readonly class SendWelcomeEmailMessageHandler implements HandlerInterface
{
    public function handle(MessageInterface $message, Printer $printer): void
    {
        $printer->out("Sending welcome email to {$message->userId}");
    }
}
```

## How a handler is resolved

When a queue consumes a message, it reads the `Handler` attribute from the message class through reflection and resolves the named handler with `Raxos\Foundation\Util\Singleton` from [raxos/foundation](/foundation/). Because `Singleton` returns a single shared instance per class, handlers should be stateless services: put the per message data in the message, not in the handler.

If the consumed message class has no `Handler` attribute, `MessageBusQueue::consume()` throws a `MessageBusMissingHandlerException`.

## Next steps

With a message and a handler in place, see [publishing and consuming](/message-bus/publishing-and-consuming) for how to put messages on a queue and run a worker that processes them.
