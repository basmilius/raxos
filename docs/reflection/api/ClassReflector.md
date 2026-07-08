---
outline: deep
---

# ClassReflector

`Raxos\Reflection\ClassReflector`

The entry point for reflecting a class. It gives typed access to a class name, its short name and file, its properties, methods and constructor, its parent and implemented interfaces, and it can create instances and call static methods. It uses the [`Attributable`](/reflection/api/Attributable) trait for attribute reading.

```php
final readonly class ClassReflector implements ReflectorInterface
```

The class implements the `ReflectorInterface` contract from [raxos/contract](/contract/).

## Methods

### `__construct()`

```php
public function __construct(string|object $class)
```

Creates a reflector from a class name, an object instance, another `ClassReflector` or a `ReflectionClass`. The global `reflect()` helper is a convenient shortcut for the string or `ReflectionClass` case.

### `getName()`

```php
public function getName(): string
```

Returns the fully qualified class name.

### `getShortName()`

```php
public function getShortName(): string
```

Returns the class name without its namespace.

### `getFileName()`

```php
public function getFileName(): string|false
```

Returns the file the class is defined in, or `false` for internal classes.

### `getInterfaces()`

```php
public function getInterfaces(): Generator
```

Yields a [`TypeReflector`](/reflection/api/TypeReflector) for each implemented interface.

### `getParent()`

```php
public function getParent(): ?self
```

Returns a `ClassReflector` for the parent class, or `null` when there is none.

### `getType()`

```php
public function getType(): TypeReflector
```

Returns a [`TypeReflector`](/reflection/api/TypeReflector) representing this class.

### `getPublicProperties()`

```php
public function getPublicProperties(): Generator
```

Yields a [`PropertyReflector`](/reflection/api/PropertyReflector) for each public property.

### `getProperties()`

```php
public function getProperties(): Generator
```

Yields a [`PropertyReflector`](/reflection/api/PropertyReflector) for every property regardless of visibility.

### `getProperty()`

```php
public function getProperty(string $name): ?PropertyReflector
```

Returns a reflector for a single named property.

### `hasProperty()`

```php
public function hasProperty(string $name): bool
```

Checks whether the class declares the given property.

### `getConstructor()`

```php
public function getConstructor(): ?MethodReflector
```

Returns a [`MethodReflector`](/reflection/api/MethodReflector) for the constructor, or `null` if there is none.

### `getPublicMethods()`

```php
public function getPublicMethods(): Generator
```

Yields a [`MethodReflector`](/reflection/api/MethodReflector) for each public method.

### `getMethods()`

```php
public function getMethods(): Generator
```

Yields a [`MethodReflector`](/reflection/api/MethodReflector) for every method regardless of visibility.

### `getMethod()`

```php
public function getMethod(string $name): ?MethodReflector
```

Returns a reflector for a single named method.

### `isInstantiable()`

```php
public function isInstantiable(): bool
```

Checks whether the class can be instantiated.

### `newInstanceArgs()`

```php
public function newInstanceArgs(array $args = []): object
```

Creates a new instance, passing the given constructor arguments.

### `newInstanceWithoutConstructor()`

```php
public function newInstanceWithoutConstructor(): object
```

Creates a new instance without invoking the constructor.

### `callStatic()`

```php
public function callStatic(string $method, mixed ...$args): mixed
```

Calls a static method on the class with the given arguments.

### `implements()`

```php
public function implements(string $interface): bool
```

Checks whether the class implements the given interface.

### `is()`

```php
public function is(string $type): bool
```

Checks whether the class matches the given class or interface name.

## Usage

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$reflector = reflect(User::class);

if ($reflector->isInstantiable()) {
    $user = $reflector->newInstanceArgs(['Bas']);
}

foreach ($reflector->getPublicMethods() as $method) {
    echo $method->getShortName() . "\n";
}
```
