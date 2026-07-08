---
outline: deep
---

# ExceptionInterface

`Raxos\Contract\ExceptionInterface` is the root exception contract that every domain exception interface in the package extends. It composes with normal PHP exception handling and adds a stable, machine readable error code plus a human readable description.

## Signature

```php
interface ExceptionInterface extends JsonSerializable, Throwable
{
    public string $error {
        get;
    }

    public string $errorDescription {
        get;
    }
}
```

## Properties

| Property | Description |
|----------|-------------|
| `string $error` | Machine readable error identifier, for example `database_connection_failed`. |
| `string $errorDescription` | Human readable description of what went wrong. |

## Notes

- It extends `JsonSerializable` and `Throwable`, so it works with `try`/`catch` as usual and can be serialized into an API error response.
- It is implemented indirectly by every concrete Raxos exception through the base `Exception` class in [raxos/error](/error/).
- Each package defines its own marker interface that extends this one. See [exception contracts](/contract/exceptions) for the full pattern.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\ExceptionInterface;

function toErrorPayload(ExceptionInterface $exception): array
{
    return [
        'error' => $exception->error,
        'description' => $exception->errorDescription,
    ];
}
```
