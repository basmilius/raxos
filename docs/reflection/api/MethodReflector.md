---
outline: deep
---

# MethodReflector

`Raxos\Reflection\MethodReflector`

Reflects a single class method: its parameters, return type and declaring class, and it can invoke the method on an instance. It uses the [`Attributable`](/reflection/api/Attributable) trait for attribute reading and implements `SerializableInterface`, so a method reference can be serialized and restored.

```php
final readonly class MethodReflector implements ReflectorInterface, SerializableInterface
```

Both `ReflectorInterface` and `SerializableInterface` come from [raxos/contract](/contract/).

## Methods

### `__construct()`

```php
public function __construct(ReflectionMethod $method)
```

Creates a reflector from a native `ReflectionMethod`. You usually obtain one from a [`ClassReflector`](/reflection/api/ClassReflector) instead of constructing it directly.

### `getParameters()`

```php
public function getParameters(): Generator
```

Yields a [`ParameterReflector`](/reflection/api/ParameterReflector) for each parameter of the method.

### `getParameter()`

```php
public function getParameter(string $name): ?ParameterReflector
```

Returns a reflector for a single named parameter, or `null` when it does not exist.

### `getClass()`

```php
public function getClass(): ClassReflector
```

Returns a [`ClassReflector`](/reflection/api/ClassReflector) for the declaring class.

### `getReturnType()`

```php
public function getReturnType(): ?TypeReflector
```

Returns a [`TypeReflector`](/reflection/api/TypeReflector) for the declared return type, or `null` when the method has none.

### `getName()`

```php
public function getName(): string
```

Returns the method name.

### `getShortName()`

```php
public function getShortName(): string
```

Returns a readable signature such as `User::rename(string $name)`.

### `invokeArgs()`

```php
public function invokeArgs(?object $instance, array $args = []): mixed
```

Invokes the method on the given instance with the given arguments. Pass `null` as the instance for static methods.

## Serialization

`MethodReflector` implements `__serialize()` and `__unserialize()`. It serializes to the declaring class name and method name, and restores itself by constructing a fresh `ReflectionMethod` from that pair. This makes it safe to store a method reference and rebuild it later.

## Usage

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$method = reflect(User::class)->getMethod('rename');

$result = $method->invokeArgs($user, ['Bas']);
```
