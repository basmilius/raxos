---
outline: deep
---

# MessageBusQueue

`Raxos\MessageBus\MessageBusQueue` represents a single AMQP queue. You publish messages to it and consume them from it. You obtain one from [MessageBus::createQueue()](/message-bus/api/MessageBus#createqueue) rather than constructing it directly.

```php
final readonly class MessageBusQueue implements MessageBusQueueInterface
```

It implements `Raxos\Contract\MessageBus\MessageBusQueueInterface`.

## Properties

- `public MessageBus $messageBus`: the bus that owns this queue.
- `public string $name`: the name of the queue.

## Constructor

```php
public function __construct(
    MessageBus $messageBus,
    string $name,
    AMQPChannel $channel,
    int $maxMessages = 25,
    array|true $allowedClasses = true
)
```

Wraps an existing AMQP channel for a named queue. `$maxMessages` is the number of messages a `consume()` loop processes before it stops. `$allowedClasses` restricts which classes may be unserialized from the queue body: pass an array of class-strings to allow only those, or leave it as `true` to allow all classes. Restricting it is recommended in production.

::: warning
`MessageBus::createQueue()` constructs the queue with `$allowedClasses` set to `true`. To restrict deserialization, construct a `MessageBusQueue` yourself with an explicit array of allowed message classes.
:::

## Methods

### publish()

```php
public function publish(MessageInterface $message, MessagePriority $priority = MessagePriority::NORMAL): void
```

Serializes the message and publishes it to the queue with persistent delivery, using the given [MessagePriority](/message-bus/api/MessagePriority). Throws a `MessageBusPublishException` on failure.

```php
use Raxos\MessageBus\Enum\MessagePriority;

$queue->publish(new SendWelcomeEmailMessage($userId), MessagePriority::HIGH);
```

### consume()

```php
public function consume(callable $callback): void
```

Runs the consumer loop. For each delivered message it deserializes the object, reads the message's `Handler` attribute, resolves the handler with `Singleton`, and calls `$callback` with the handler and the message. The callback signature is `callable(HandlerInterface $handler, MessageInterface $message): bool`.

Returning `true` acknowledges the message; returning `false` or throwing requeues it. A thrown exception is rethrown as a `MessageBusConsumeException`. The loop stops after `maxMessages` messages. If a consumed message has no `Handler` attribute, a `MessageBusMissingHandlerException` is thrown.

::: info Non message bodies
A delivered body that does not deserialize into a `MessageInterface` instance is rejected with `nack` (requeue `false`) before your callback runs, unlike the callback driven ack or nack behavior described above for normal messages. The message is dropped without invoking the callback and without throwing, and it is not counted against `maxMessages`.
:::

```php
use Raxos\Contract\MessageBus\{HandlerInterface, MessageInterface};

$queue->consume(fn(HandlerInterface $handler, MessageInterface $message): bool => true);
```

### close()

```php
public function close(): void
```

Removes the queue from its `MessageBus` and closes the underlying channel.

## See also

- [Messages and handlers](/message-bus/messages-and-handlers): how a message is linked to its handler.
- [Publishing and consuming](/message-bus/publishing-and-consuming): the full lifecycle in context.
