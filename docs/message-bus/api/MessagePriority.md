---
outline: deep
---

# MessagePriority

`Raxos\MessageBus\Enum\MessagePriority` is a backed integer enum with the five priority levels you can set when publishing a message. The value maps directly to the AMQP priority of the message, and queues are declared with a maximum priority of five so all levels are honoured.

```php
enum MessagePriority: int
{
    case VERY_LOW = 1;
    case LOW = 2;
    case NORMAL = 3;
    case HIGH = 4;
    case VERY_HIGH = 5;
}
```

## Cases

| Case        | Value |
|-------------|-------|
| `VERY_LOW`  | `1`   |
| `LOW`       | `2`   |
| `NORMAL`    | `3`   |
| `HIGH`      | `4`   |
| `VERY_HIGH` | `5`   |

`NORMAL` is the default used by [MessageBusQueue::publish()](/message-bus/api/MessageBusQueue#publish) when no priority is given. Higher priority messages waiting on a queue are delivered before lower priority ones.

## Usage

```php
use Raxos\MessageBus\Enum\MessagePriority;

$queue->publish(new SendWelcomeEmailMessage($userId), MessagePriority::HIGH);
$queue->publish(new RebuildSearchIndexMessage(), MessagePriority::VERY_LOW);
```

## See also

- [Publishing and consuming](/message-bus/publishing-and-consuming): how priority affects delivery.
- [MessageBusQueue](/message-bus/api/MessageBusQueue): the `publish()` method that accepts a priority.
