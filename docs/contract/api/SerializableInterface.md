---
outline: deep
---

# SerializableInterface

`Raxos\Contract\SerializableInterface` marks a class that controls its own PHP serialize and unserialize behavior. It is used, for example, by message bus messages that travel through a queue.

## Signature

```php
interface SerializableInterface
{
    public function __serialize(): array;

    public function __unserialize(array $data): void;
}
```

## Methods

### `__serialize(): array`

Serializes the object into a plain array. Matches PHP's own magic serialization method.

### `__unserialize(array $data): void`

Restores the object state from a previously serialized array. Matches PHP's own magic method.

## Notes

- `MessageInterface` in [raxos/message-bus](/message-bus/) extends this contract, so messages can be serialized onto a queue and restored later.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\SerializableInterface;

final class ImportJob implements SerializableInterface
{
    public function __construct(
        public string $fileName,
        public int $attempts = 0,
    ) {}

    public function __serialize(): array
    {
        return [
            'fileName' => $this->fileName,
            'attempts' => $this->attempts,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->fileName = $data['fileName'];
        $this->attempts = $data['attempts'];
    }
}
```
