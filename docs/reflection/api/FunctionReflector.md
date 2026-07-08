---
outline: deep
---

# FunctionReflector

`Raxos\Reflection\FunctionReflector`

Reflects a standalone function or a closure. It exposes the function name, its file location and its parameters, and it can invoke the function with the given arguments. It uses the [`Attributable`](/reflection/api/Attributable) trait for attribute reading.

```php
final readonly class FunctionReflector implements ReflectorInterface
```

The class implements the `ReflectorInterface` contract from [raxos/contract](/contract/).

## Methods

### `__construct()`

```php
public function __construct(ReflectionFunction|Closure $function)
```

Creates a reflector from a `Closure` or a native `ReflectionFunction`.

### `getName()`

```php
public function getName(): string
```

Returns the function name.

### `getShortName()`

```php
public function getShortName(): string
```

Returns the short name of the function.

### `getFileName()`

```php
public function getFileName(): string
```

Returns the file the function is defined in.

### `getStartLine()`

```php
public function getStartLine(): int
```

Returns the line the function declaration starts on.

### `getEndLine()`

```php
public function getEndLine(): int
```

Returns the line the function declaration ends on.

### `invokeArgs()`

```php
public function invokeArgs(array $args = []): mixed
```

Invokes the function with the given arguments.

### `getParameters()`

```php
public function getParameters(): Generator
```

Yields a [`ParameterReflector`](/reflection/api/ParameterReflector) for each parameter.

### `getParameter()`

```php
public function getParameter(int|string $key): ?ParameterReflector
```

Returns a reflector for a parameter by name or position, or `null` when it does not exist.

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\Reflection\FunctionReflector;

$reflector = new FunctionReflector(static fn(int $value): int => $value * 2);

foreach ($reflector->getParameters() as $parameter) {
    echo $parameter->getName() . "\n";
}

echo $reflector->invokeArgs([21]); // 42
```
