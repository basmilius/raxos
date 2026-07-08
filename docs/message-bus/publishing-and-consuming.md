---
outline: deep
---

# Publishing and consuming

Message Bus has two moving parts: `MessageBus` owns the AMQP connection and hands out queues, and `MessageBusQueue` represents one queue you publish to and consume from. Publishing code and consuming code use the same classes but run in different processes.

## Connecting and creating a queue

Construct a `MessageBus` with the RabbitMQ connection details. The constructor opens the connection immediately and throws a `MessageBusConnectionException` if it cannot connect.

Call `createQueue()` to declare a queue and get a `MessageBusQueue` back. The queue is declared as durable and with priority support, so it survives a broker restart and honours the priority you set when publishing.

```php
<?php
declare(strict_types=1);

use Raxos\MessageBus\MessageBus;

$bus = new MessageBus(
    host: 'localhost',
    port: 5672,
    username: 'guest',
    password: 'guest'
);

$queue = $bus->createQueue('task_queue');
```

The queue name defaults to `task_queue`. The second argument, `maxMessages`, defaults to `25` and controls how many messages a consumer processes before it stops (see [Consuming messages](#consuming-messages) below).

## Publishing a message

`publish()` serializes the message and sends it to the queue with persistent delivery, so it is written to disk and not lost if the broker restarts. Pass a `MessagePriority` to influence the order in which waiting messages are delivered; it defaults to `MessagePriority::NORMAL`.

```php
use Raxos\MessageBus\Enum\MessagePriority;

$queue->publish(new SendWelcomeEmailMessage($userId));
$queue->publish(new RebuildSearchIndexMessage(), MessagePriority::VERY_LOW);
```

See [MessagePriority](/message-bus/api/MessagePriority) for the five available levels.

## Consuming messages

`consume()` runs a worker loop. For each message it deserializes the object, resolves the handler from the message's `Handler` attribute, and invokes your callback with that handler and the message. The callback returns a `bool` that decides what happens to the message:

- Return `true` to acknowledge the message. It is removed from the queue.
- Return `false` to requeue the message so it is delivered again.
- Throw an exception to requeue the message; the loop then rethrows it as a `MessageBusConsumeException`.

```php
<?php
declare(strict_types=1);

use Raxos\Contract\MessageBus\{HandlerInterface, MessageInterface};
use Raxos\Terminal\Printer;

$queue->consume(function (HandlerInterface $handler, MessageInterface $message) use ($printer): bool {
    try {
        $handler->handle($message, $printer);

        return true;
    } catch (\Throwable $err) {
        $printer->incorrect($err->getMessage());

        return false;
    }
});
```

The loop stops once it has processed `maxMessages` messages, the value you passed to `createQueue()`. Running a worker for a bounded number of messages and then letting a process manager restart it is a common way to keep long running consumers healthy.

::: info
The prefetch count is set to one, so a consumer only holds a single unacknowledged message at a time. Work is spread evenly when you run more than one worker against the same queue.
:::

## Messages that fail to deserialize

Before your callback ever runs, `consume()` deserializes the raw queue body back into an object. When that body cannot be turned into a `MessageInterface` instance, the message is dropped silently. This happens for a foreign payload published by some other system, for a corrupted body, or for a class that is not in the queue's `allowedClasses` list.

In that case the consumer negatively acknowledges the delivery with requeue set to `false`, then returns. Your callback is never invoked, no handler is resolved, and no exception is thrown or logged. The message leaves the queue for good instead of being redelivered in a loop.

Such a message also does not count toward `maxMessages`. The counter only advances after your callback has run for a real message, so a batch of undeliverable payloads will not shorten the worker's run. If messages seem to vanish without reaching a handler and without any error surfacing, an undeserializable body is the likely cause. Restricting `allowedClasses` to your own message classes makes this behavior explicit rather than accidental.

## Closing down

Each queue owns an AMQP channel. Call `MessageBusQueue::close()` to release a single queue's channel, or `MessageBus::close()` to close every queue the bus created and then the underlying connection.

```php
$queue->close();
// or, to close everything at once:
$bus->close();
```

## API reference

- [MessageBus](/message-bus/api/MessageBus): the connection and queue factory.
- [MessageBusQueue](/message-bus/api/MessageBusQueue): publish, consume and close a single queue.
- [Exceptions](/message-bus/api/Exceptions): the typed failures the package throws.
