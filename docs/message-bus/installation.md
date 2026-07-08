---
outline: deep
---

# Installation

Install Message Bus with Composer.

```shell
composer require raxos/message-bus
```

## Requirements

- PHP 8.5 or newer.
- A reachable RabbitMQ server to connect to.
- The `php-amqplib/php-amqplib` library (installed for you), which implements the AMQP protocol used to talk to RabbitMQ.

## Raxos dependencies

Message Bus builds on a few other Raxos packages, which Composer installs for you:

- [raxos/contract](/contract/): the `MessageInterface`, `HandlerInterface` and message bus interfaces the package implements.
- [raxos/error](/error/): the base exception class that every Message Bus exception extends.
- [raxos/foundation](/foundation/): the `Singleton` utility used to resolve handlers, and the `ArrayList` used to track open queues.
- [raxos/terminal](/terminal/): the `Printer` passed to a handler for command line output.

Return to the [Message Bus introduction](/message-bus/).
