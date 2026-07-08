---
outline: deep
---

# Access traits

Foundation ships two traits that wire up PHP's access protocols to a single, small set of accessor methods. Instead of implementing `ArrayAccess` or the magic `__get`, `__set`, `__isset` and `__unset` methods yourself, you implement four accessor methods once and let the trait translate the protocol calls into them.

The two traits are `Raxos\Foundation\Access\ArrayAccessible` and `Raxos\Foundation\Access\ObjectAccessible`.

## The four accessor methods

Both traits delegate to methods that your host class must define:

- `getValue($offset)`: return the value at the given key.
- `setValue($offset, $value)`: store a value at the given key.
- `hasValue($offset)`: return whether a value exists at the given key.
- `unsetValue($offset)`: remove the value at the given key.

The traits themselves only provide the protocol wiring. They do not decide where or how the values are stored, that is entirely up to your class.

## ArrayAccessible

`ArrayAccessible` implements the `ArrayAccess` methods `offsetGet`, `offsetSet`, `offsetExists` and `offsetUnset`, each delegating to the matching accessor method. Add `implements ArrayAccess` to your class so PHP recognises the array syntax.

```php
<?php
declare(strict_types=1);

use ArrayAccess;
use Raxos\Foundation\Access\ArrayAccessible;

/**
 * @implements ArrayAccess<string, mixed>
 */
final class Bag implements ArrayAccess
{
    use ArrayAccessible;

    private array $values = [];

    public function getValue(mixed $offset): mixed
    {
        return $this->values[$offset] ?? null;
    }

    public function setValue(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    public function hasValue(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function unsetValue(mixed $offset): void
    {
        unset($this->values[$offset]);
    }
}

$bag = new Bag();
$bag['name'] = 'Raxos';

isset($bag['name']); // true
$bag['name'];        // 'Raxos'
unset($bag['name']);
```

## ObjectAccessible

`ObjectAccessible` implements the magic methods `__get`, `__set`, `__isset` and `__unset`, delegating to the same four accessor methods. No interface is required for magic property access.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Access\ObjectAccessible;

final class Config
{
    use ObjectAccessible;

    private array $values = [];

    public function getValue(mixed $offset): mixed
    {
        return $this->values[$offset] ?? null;
    }

    public function setValue(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    public function hasValue(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function unsetValue(mixed $offset): void
    {
        unset($this->values[$offset]);
    }
}

$config = new Config();
$config->timeout = 30;

isset($config->timeout); // true
$config->timeout;        // 30
```

## Combining both traits

Because both traits share the same four accessor methods, you can use them together to support array syntax and property syntax at once on the same class.

```php
<?php
declare(strict_types=1);

use ArrayAccess;
use Raxos\Foundation\Access\{ArrayAccessible, ObjectAccessible};

/**
 * @implements ArrayAccess<string, mixed>
 */
final class Store implements ArrayAccess
{
    use ArrayAccessible;
    use ObjectAccessible;

    private array $values = [];

    public function getValue(mixed $offset): mixed
    {
        return $this->values[$offset] ?? null;
    }

    public function setValue(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
    }

    public function hasValue(mixed $offset): bool
    {
        return isset($this->values[$offset]);
    }

    public function unsetValue(mixed $offset): void
    {
        unset($this->values[$offset]);
    }
}

$store = new Store();
$store['token'] = 'abc';
$store->token; // 'abc'
```

::: tip
The accessor methods can throw. Both traits declare that their protocol methods may throw `Throwable`, so any exception from `getValue`, `setValue`, `hasValue` or `unsetValue` surfaces through the array or property access.
:::
