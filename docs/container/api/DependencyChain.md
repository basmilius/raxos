---
outline: deep
---

# DependencyChain

`Raxos\Container\DependencyChain` records how the container arrived at a failure. It stores where resolution began (the file and line of a `resolve()` or `call()`) and the ordered path of `Dependency` entries it visited on the way. Outside production mode the container attaches a chain to the exceptions that describe a missing or unresolvable dependency, which makes it the primary tool for understanding why a `get` or `call` failed.

For where these chains surface, see [Errors and dependency chains](/container/errors).

## DependencyChain

### Signature

```php
final class DependencyChain implements ArrayableInterface, DebuggableInterface
{
    public readonly string $fileName;
    public readonly int $line;

    public function __construct(string $fileName, int $line);

    public function add(ReflectorInterface|Closure|string $dep): void;
    public function first(): Dependency;
    public function last(): Dependency;
    public function toArray(): array;
}
```

Both `ArrayableInterface` and `DebuggableInterface` come from [Contract](/contract/). The first gives you `toArray()`, the second wires up `__debugInfo()` so a `var_dump` of the chain prints the origin and the visited dependencies.

### Properties

| Property | Type | Description |
| --- | --- | --- |
| `fileName` | `string` | The file where resolution started, taken from the `resolve()` or `call()` site. |
| `line` | `int` | The line within that file. |

### Methods

#### `add()`

```php
public function add(ReflectorInterface|Closure|string $dep): void
```

Appends a dependency to the chain. The value is wrapped in a `Dependency`, then stored keyed by its resolved name. If a dependency with that name is already present, the container is going in a circle, so `add()` throws a `CircularDependencyDetectedException`. When the underlying reflection fails, it throws a `ReflectionFailedException` instead.

#### `first()` and `last()`

```php
public function first(): Dependency
public function last(): Dependency
```

Return the `Dependency` at each end of the chain. `first()` is where resolution began, `last()` is the dependency that was being resolved when the failure occurred.

#### `toArray()`

```php
public function toArray(): array
```

Returns the full ordered list of `Dependency` objects, keyed by each dependency name. Walk it to print the complete resolution path.

### Example

```php
<?php
declare(strict_types=1);

use Raxos\Container\Error\DependencyCannotAutowireException;

try {
    $container->get(ReportService::class);
} catch (DependencyCannotAutowireException $err) {
    $chain = $err->chain;

    if ($chain !== null) {
        echo "Started at {$chain->fileName}:{$chain->line}\n";

        foreach ($chain->toArray() as $dependency) {
            echo "  -> {$dependency->name}\n";
        }

        echo "Failed on {$chain->last()->name}\n";
    }
}
```

::: tip
The `chain` property is only populated when the container runs with `production: false`. In production the tracking is skipped and the property is `null`, so guard for that before reading it.
:::

## Dependency

`Raxos\Container\Dependency` is a single node in a chain. It is a `final readonly` value object describing one class, function, method, parameter or type that the container touched.

### Signature

```php
final readonly class Dependency
{
    public string $name;
    public string $shortName;
    public string $typeName;

    public function __construct(ReflectorInterface|Closure|string $dep);

    public function equals(Dependency $other): bool;
}
```

A `Dependency` is built from a `ClassReflector`, `FunctionReflector`, `MethodReflector`, `ParameterReflector` or `TypeReflector` (all from [Reflection](/reflection/)), or from a plain class string. The reflectors implement `ReflectorInterface` from [Contract](/contract/).

### Properties

| Property | Type | Description |
| --- | --- | --- |
| `name` | `string` | The fully qualified name of the dependency, used as its identity in the chain. |
| `shortName` | `string` | The short, unqualified name for display. |
| `typeName` | `string` | The short type name of the dependency. |

### Methods

#### `equals()`

```php
public function equals(Dependency $other): bool
```

Returns `true` when both dependencies share the same `name`. Use it to compare two nodes without matching on object identity.

### Example

```php
<?php
declare(strict_types=1);

$dependency = $chain->last();

echo $dependency->name;      // App\Reporting\ReportService
echo $dependency->shortName; // ReportService
echo $dependency->typeName;  // ReportService
```
