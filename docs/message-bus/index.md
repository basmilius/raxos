---
outline: deep

cards:
    highlights:
        -   title: MessageBus
            code: true
            details: 'Owns the AMQP connection and creates the queues you publish to and consume from.'
            link: /message-bus/api/MessageBus
        -   title: MessageBusQueue
            code: true
            details: 'A single queue that publishes messages and consumes them with an acknowledge callback.'
            link: /message-bus/api/MessageBusQueue
        -   title: Handler
            code: true
            details: 'Class attribute that links a message to the handler that processes it.'
            link: /message-bus/api/Handler
        -   title: MessagePriority
            code: true
            details: 'Five priority levels applied to a message when you publish it.'
            link: /message-bus/api/MessagePriority
---

# Message Bus

A RabbitMQ backed message bus for publishing and consuming background work.

Message Bus wraps php-amqplib to give Raxos applications a small, typed API for pushing work onto a queue and consuming it later. Messages are plain PHP objects that implement `MessageInterface` and carry a `Handler` attribute pointing at the class that processes them, so publishing and consuming code never needs a routing table. The package supports five priority levels, persistent delivery, a per worker message limit, and a small set of typed exceptions for connection, publish, consume and timeout failures.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Messages and handlers](/message-bus/messages-and-handlers): define a message class, mark it with a `Handler` attribute, and write the handler that processes it.
- [Publishing and consuming](/message-bus/publishing-and-consuming): connect to RabbitMQ, create a queue, publish with a priority, and consume with an acknowledge callback.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\MessageBus\{MessageBus, Enum\MessagePriority};

$bus = new MessageBus(
    host: 'localhost',
    port: 5672,
    username: 'guest',
    password: 'guest'
);

$queue = $bus->createQueue('task_queue');
$queue->publish(new SendWelcomeEmailMessage($userId), MessagePriority::HIGH);
```

## Installation

Install it with Composer.

```shell
composer require raxos/message-bus
```

See [installation](/message-bus/installation) for requirements, or use the sidebar to navigate this package.
