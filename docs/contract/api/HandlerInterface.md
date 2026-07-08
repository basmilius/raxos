---
outline: deep
---

# HandlerInterface (Message Bus)

`Raxos\Contract\MessageBus\HandlerInterface` is an extension point for message bus handlers in [raxos/message-bus](/message-bus/). Each handler processes one message type dispatched through the bus or a queue.

## Signature

```php
interface HandlerInterface
{
    public function handle(MessageInterface $message, Printer $printer): void;
}
```

The interface is templated (`@template TMessage of MessageInterface`), so an implementation handles one specific message type, and `handle` may throw any `Throwable`.

## Methods

### `handle(MessageInterface $message, Printer $printer): void`

Processes the given message, optionally writing output through the terminal `Printer`.

## Notes

- One handler is written per message type, matched through the `MessageInterface` the message implements.
- It is used both for messages dispatched directly and for messages processed from a queue by raxos/message-bus.
- The `Printer` type comes from [raxos/terminal](/terminal/), which lets a handler that runs from the command line write progress output.

## Example

```php
<?php
declare(strict_types=1);

namespace App\MessageBus;

use Override;
use Raxos\Contract\MessageBus\{HandlerInterface, MessageInterface};
use Raxos\Terminal\Printer;

final readonly class SendWelcomeEmailHandler implements HandlerInterface
{
    #[Override]
    public function handle(MessageInterface $message, Printer $printer): void
    {
        // $message is a SendWelcomeEmail instance implementing MessageInterface.

        // ... deliver the email ...

        $printer->correct('Welcome email sent');
    }
}
```
