---
outline: deep

cards:
    highlights:
        -   title: ClassReflector
            code: true
            details: 'Reflects a class and exposes its properties, methods, constructor and hierarchy as typed reflectors.'
            link: /reflection/api/ClassReflector
        -   title: TypeReflector
            code: true
            details: 'Normalizes and validates a PHP type, including builtins, classes, enums, unions and intersections.'
            link: /reflection/api/TypeReflector
        -   title: Attributable
            code: true
            details: 'Adds consistent attribute reading to every reflector through one shared trait.'
            link: /reflection/api/Attributable
        -   title: PropertyReflector
            code: true
            details: 'Reads, writes and introspects a single property on a given object instance.'
            link: /reflection/api/PropertyReflector
        -   title: MethodReflector
            code: true
            details: 'Reflects a method, its parameters and return type, and can invoke it on an instance.'
            link: /reflection/api/MethodReflector
        -   title: FunctionReflector
            code: true
            details: 'Reflects a closure or standalone function and invokes it with the given arguments.'
            link: /reflection/api/FunctionReflector
---

# Reflection

Raxos Reflection wraps PHP's native `ReflectionClass`, `ReflectionMethod`, `ReflectionProperty`, `ReflectionParameter` and `ReflectionFunction` in small, readonly reflector classes with a consistent, typed API. Every reflector can read PHP attributes through the shared `Attributable` trait, and a dedicated `TypeReflector` introspects and validates types, including unions, intersections, enums and iterables. The package is a foundational dependency used across Raxos for attribute driven features such as ORM models, router controllers and dependency injection.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Reflectors](/reflection/reflectors): obtain a `ClassReflector` with the `reflect()` helper and walk a class, its properties, methods and hierarchy as typed reflector objects.
- [Reading attributes](/reflection/attributes): how the `Attributable` trait exposes PHP attribute instances the same way on every reflector.
- [Working with types](/reflection/types): use `TypeReflector` to classify and validate PHP types, from scalars and classes to enums, unions and intersections.

## Quick example

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$reflector = reflect(User::class);

foreach ($reflector->getPublicProperties() as $property) {
    echo $property->getName() . ': ' . $property->getType()->getName() . "\n";
}
```

## Installation

Install the package with Composer and check the requirements on the [installation](/reflection/installation) page.

```shell
composer require raxos/reflection
```
