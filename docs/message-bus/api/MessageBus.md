---
outline: deep
---

# MessageBus

`Raxos\MessageBus\MessageBus` owns the AMQP connection and acts as a factory for queues. Construct one per process, create the queues you need from it, and close it when you are done.

```php
final readonly class MessageBus implements MessageBusInterface
```

It implements `Raxos\Contract\MessageBus\MessageBusInterface`.

## Constructor

```php
public function __construct(
    string $host,
    int $port,
    string $username,
    string $password,
    string $vhost = '/'
)
```

Opens an AMQP stream connection to the given RabbitMQ server. The `$vhost` defaults to `/`. If the connection cannot be opened, the constructor throws a `MessageBusConnectionException`. The host, port, username and password are marked as sensitive parameters so they are redacted in stack traces.

```php
use Raxos\MessageBus\MessageBus;

$bus = new MessageBus(
    host: 'localhost',
    port: 5672,
    username: 'guest',
    password: 'guest',
    vhost: '/'
);
```

## Methods

### createQueue()

```php
public function createQueue(string $name = 'task_queue', int $maxMessages = 25): MessageBusQueueInterface
```

Declares a durable, priority aware queue on the connection and returns a [MessageBusQueue](/message-bus/api/MessageBusQueue) bound to it. The queue is tracked by the bus so `close()` can clean it up later. `$maxMessages` sets how many messages a consumer on the returned queue processes before it stops. Throws a `MessageBusTimeoutException` if the broker does not respond in time.

```php
$queue = $bus->createQueue('emails', maxMessages: 50);
```

### removeQueue()

```php
public function removeQueue(MessageBusQueueInterface $queue): void
```

Stops tracking a queue that was created earlier. This is called for you by `MessageBusQueue::close()`; you rarely need to call it directly. It does not close the queue's channel on its own.

### close()

```php
public function close(): void
```

Closes every tracked queue and then the underlying AMQP connection. Throws a `MessageBusConnectionException` if closing fails.

```php
$bus->close();
```

## See also

- [MessageBusQueue](/message-bus/api/MessageBusQueue): the queue returned by `createQueue()`.
- [Publishing and consuming](/message-bus/publishing-and-consuming): the full lifecycle in context.
- [Exceptions](/message-bus/api/Exceptions): the failures these methods can throw.
