# Confirmed delivery and retries

Queues keep their legacy boolean-consumer behavior unless a `QueuePolicy` is supplied.

```php
use Raxos\MessageBus\DeliveryOutcome;
use Raxos\MessageBus\QueuePolicy;

$queue = $bus->createQueue(
    name: 'jobs',
    allowedClasses: [ProcessJob::class],
    policy: new QueuePolicy(maxAttempts: 3, prefetch: 1, retryDelay: 1)
);

$queue->consume(static function ($handler, ProcessJob $message): DeliveryOutcome {
    $handler->process($message);
    return DeliveryOutcome::ack();
});
```

With a policy, `true` means acknowledgement, while `false` and handler exceptions request a retry. `DeliveryOutcome::retry()` uses the default delay; `retry(5)` selects an allowed delay. `reject()` transfers directly to `<queue>.dead`. Attempts include the initial delivery. The default maximum is three attempts; the final failure goes to the dead queue.

Allowed delays default to 0, 1, 5 and 30 seconds. Zero requeues through a confirmed publication. Positive delays use durable quorum retry queues with TTL and at-least-once dead-letter routing back to the original queue. The broker must support these settings; the native suite uses RabbitMQ 4. Use a separate channel for a policy queue because it owns that channel's confirmation callbacks.

Publications require publisher confirmation and mandatory routing. Retry and dead-queue transfers must be confirmed before the original message is acknowledged. A missing route or failed confirmation leaves the original unacknowledged; closing the channel permits redelivery. Transfer and acknowledgement cannot form one atomic broker transaction, so duplicate processing remains possible. Make handlers idempotent.

The optional `handlerResolver` Closure can use an application container. It receives the handler class. Without it, existing Singleton resolution remains active. Prefetch and confirmation timeout are explicit policy settings.

Register all permitted message and nested value-object classes. Registration permits their unserialization hooks. Malformed or unauthorized root objects are rejected before handler dispatch and count toward the worker's maximum message limit. Without a policy, `false` and handler failures retain immediate requeue behavior.
