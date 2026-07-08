---
outline: deep
---

# ParameterReflector

`Raxos\Reflection\ParameterReflector`

Reflects a single parameter of a function or method. It exposes the declaring class or function, the parameter type, its position and default value, and a set of predicates that describe how the parameter can be called. It uses the [`Attributable`](/reflection/api/Attributable) trait for attribute reading.

```php
final readonly class ParameterReflector implements ReflectorInterface
```

The class implements the `ReflectorInterface` contract from [raxos/contract](/contract/).

## Methods

### `__construct()`

```php
public function __construct(ReflectionParameter $parameter)
```

Creates a reflector from a native `ReflectionParameter`. You usually obtain one from a [`MethodReflector`](/reflection/api/MethodReflector) or [`FunctionReflector`](/reflection/api/FunctionReflector).

### `getClass()`

```php
public function getClass(): ?ClassReflector
```

Returns a [`ClassReflector`](/reflection/api/ClassReflector) for the declaring class, or `null` when the parameter belongs to a standalone function.

### `getFunction()`

```php
public function getFunction(): FunctionReflector|MethodReflector
```

Returns a reflector for the declaring function or method.

### `getDefaultValue()`

```php
public function getDefaultValue(): mixed
```

Returns the default value of the parameter.

### `getName()`

```php
public function getName(): string
```

Returns the parameter name.

### `getPosition()`

```php
public function getPosition(): int
```

Returns the zero based position of the parameter.

### `getType()`

```php
public function getType(): TypeReflector
```

Returns a [`TypeReflector`](/reflection/api/TypeReflector) for the parameter's declared type.

### `hasDefaultValue()`

```php
public function hasDefaultValue(): bool
```

Checks whether the parameter has a default value.

### `hasType()`

```php
public function hasType(): bool
```

Checks whether the parameter has a declared type.

### `isIterable()`

```php
public function isIterable(): bool
```

Checks whether the parameter's type is iterable.

### `isNullable()`

```php
public function isNullable(): bool
```

Checks whether the parameter's type allows `null`.

### `isOptional()`

```php
public function isOptional(): bool
```

Checks whether the parameter is optional.

### `isRequired()`

```php
public function isRequired(): bool
```

Checks whether the parameter must always be provided. A parameter is required when its type does not allow `null` and it is not optional.

### `isVariadic()`

```php
public function isVariadic(): bool
```

Checks whether the parameter is variadic.

## Usage

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$method = reflect(User::class)->getConstructor();

foreach ($method->getParameters() as $parameter) {
    echo $parameter->getPosition() . ': ' . $parameter->getName();
    echo $parameter->isRequired() ? ' (required)' : ' (optional)';
    echo "\n";
}
```
