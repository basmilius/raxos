---
outline: deep
---

# Reflectors

The reflectors are the heart of the package. Each one wraps a single native reflection object (`ReflectionClass`, `ReflectionMethod`, `ReflectionProperty`, `ReflectionParameter` or `ReflectionFunction`) in a small, immutable `final readonly class` that exposes a typed API. Instead of returning raw arrays and native reflection objects, the reflectors return other reflectors, so you can navigate a class, its methods, their parameters and their types without leaving the typed world.

## Obtaining a class reflector

The `reflect()` helper is the usual entry point. It accepts a class name or an existing `ReflectionClass` and returns a [`ClassReflector`](/reflection/api/ClassReflector).

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$reflector = reflect(User::class);

echo $reflector->getName();       // Full\Qualified\User
echo $reflector->getShortName();  // User
```

The `ClassReflector` constructor is a little broader than the helper: it also accepts an object instance or another `ClassReflector`, which is convenient when you already hold an instance.

```php
$reflector = new ClassReflector($user); // an object instance
```

## Walking properties and methods

`ClassReflector` exposes properties, methods and the constructor as generators of reflector objects rather than raw arrays. Each yielded value is itself a reflector you can drill into further.

```php
foreach ($reflector->getProperties() as $property) {
    echo $property->getName() . ': ' . $property->getType()->getName() . "\n";
}

foreach ($reflector->getPublicMethods() as $method) {
    echo $method->getShortName() . "\n";
}

$constructor = $reflector->getConstructor(); // ?MethodReflector
```

Use `getPublicProperties()` and `getPublicMethods()` to limit the result to public members, or `getProperty()` and `getMethod()` to fetch a single named member.

## Navigating the hierarchy

`getParent()` returns a `ClassReflector` for the parent class, or `null`, and `getInterfaces()` yields a [`TypeReflector`](/reflection/api/TypeReflector) for each implemented interface. `implements()` and `is()` answer hierarchy questions directly.

```php
$parent = $reflector->getParent();          // ?ClassReflector

if ($reflector->implements(JsonSerializable::class)) {
    // ...
}
```

## Parameters, functions and types

A [`MethodReflector`](/reflection/api/MethodReflector) exposes its parameters as [`ParameterReflector`](/reflection/api/ParameterReflector) instances and its return type as a [`TypeReflector`](/reflection/api/TypeReflector). A [`FunctionReflector`](/reflection/api/FunctionReflector) wraps a closure or standalone function the same way `ClassReflector` wraps a class.

```php
<?php
declare(strict_types=1);

use Raxos\Reflection\FunctionReflector;

$fn = new FunctionReflector(static fn(int $value): int => $value * 2);

foreach ($fn->getParameters() as $parameter) {
    echo $parameter->getName() . "\n";
}

echo $fn->invokeArgs([21]); // 42
```

## Immutable value objects

Every reflector is a `final readonly class` that holds the underlying `Reflection*` instance. They carry no mutable state, so they are safe to pass around and cache. The one exception in behavior is `MethodReflector`, which implements `SerializableInterface` so a method reference can be serialized and restored from its declaring class name and method name.

See the [API reference](/reflection/api/ClassReflector) for the full surface of each reflector.
