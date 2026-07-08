---
outline: deep
---

# DebuggableInterface

`Raxos\Contract\DebuggableInterface` marks a class that customizes the array PHP shows for it during `var_dump`, so debugging output stays readable instead of dumping every internal property.

## Signature

```php
interface DebuggableInterface
{
    public function __debugInfo(): array;
}
```

## Methods

### `__debugInfo(): array`

Returns the debug representation of the object. It matches PHP's own magic method of the same name, so implementing the interface makes the intent explicit while letting PHP call it automatically during `var_dump`.

## Notes

- Useful on value objects and models that hold large or sensitive internal state you do not want dumped as is.
- Implemented across Raxos by types in [raxos/foundation](/foundation/) and other packages that expose rich internal state.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Contract\DebuggableInterface;

final class ApiClient implements DebuggableInterface
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
    ) {}

    public function __debugInfo(): array
    {
        return [
            'baseUrl' => $this->baseUrl,
            'apiKey' => '***redacted***',
        ];
    }
}
```
