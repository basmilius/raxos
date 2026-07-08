---
outline: deep
---

# Exceptions

Message Bus throws five typed exceptions, all in the `Raxos\MessageBus\Error` namespace. Every one extends the base `Raxos\Error\Exception` from [raxos/error](/error/) and implements `Raxos\Contract\MessageBus\MessageBusExceptionInterface`, so you can catch any failure from the package with a single `catch (MessageBusExceptionInterface $err)`.

## MessageBusConnectionException

```php
final class MessageBusConnectionException extends Exception implements MessageBusExceptionInterface
```

Thrown when opening or closing the AMQP connection fails. This surfaces from the `MessageBus` constructor when the broker cannot be reached, and from `MessageBus::close()` when the connection or a queue cannot be closed cleanly. The underlying error is attached as the previous exception.

## MessageBusPublishException

```php
final class MessageBusPublishException extends Exception implements MessageBusExceptionInterface
```

Thrown when publishing a message fails, for example if the channel is broken while `MessageBusQueue::publish()` serializes and sends the message.

## MessageBusConsumeException

```php
final class MessageBusConsumeException extends Exception implements MessageBusExceptionInterface
```

Thrown when consuming a message fails. This wraps both errors raised inside a handler callback and errors in the AMQP consume loop itself. When a handler throws, the message is requeued before this exception is rethrown.

## MessageBusTimeoutException

```php
final class MessageBusTimeoutException extends Exception implements MessageBusExceptionInterface
```

Thrown when an AMQP operation times out, such as declaring a queue in `MessageBus::createQueue()`. It wraps the underlying `AMQPTimeoutException`.

## MessageBusMissingHandlerException

```php
final class MessageBusMissingHandlerException extends Exception implements MessageBusExceptionInterface
```

Thrown by `MessageBusQueue::consume()` when a consumed message class has no `Handler` attribute, so the bus cannot find a handler for it. The message class name is included in the error description.

## Catching failures

```php
use Raxos\Contract\MessageBus\MessageBusExceptionInterface;

try {
    $queue->publish($message);
} catch (MessageBusExceptionInterface $err) {
    // Handle any Message Bus failure.
}
```

## See also

- [Publishing and consuming](/message-bus/publishing-and-consuming): where each exception can occur.
- [raxos/error](/error/): the base exception class these extend.
