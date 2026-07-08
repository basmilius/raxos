---
outline: deep
---

# Working with types

[`TypeReflector`](/reflection/api/TypeReflector) represents a single resolved PHP type and answers questions about it: what kind of type it is, whether it accepts a given value, and how it relates to other types. It understands builtins, classes, interfaces, enums, unions and intersections.

## Creating a type reflector

The constructor normalizes several inputs into a single type definition: a native `ReflectionType`, a `ReflectionParameter`, a `ReflectionProperty`, a `ReflectionClass`, any other `Reflector`, or a plain type string.

```php
<?php
declare(strict_types=1);

use Raxos\Reflection\TypeReflector;

$type = new TypeReflector('int|string');
$type = new TypeReflector(User::class);
```

In practice you usually get one from another reflector, for example a property or parameter type.

```php
use function Raxos\Reflection\reflect;

$type = reflect(User::class)->getProperty('id')->getType();

echo $type->getName();      // int
echo $type->getShortName(); // int
```

## Validating values

`accepts()` checks whether a runtime value matches the type. It handles builtins through their native `is_*` checks, class types through `instanceof`, iterables, and both union and intersection members.

```php
$type = new TypeReflector('int|string');

$type->accepts(42);    // true
$type->accepts('foo'); // true
$type->accepts(3.14);  // false
```

Nullable types accept `null` in addition to their declared type.

```php
$type = new TypeReflector('?User');

$type->accepts(null); // true
```

## Classifying a type

A group of predicates describe what kind of type it is.

```php
$type = new TypeReflector(Status::class);

$type->isBuiltIn();    // false
$type->isClass();      // false, an enum is not a plain class
$type->isEnum();       // true
$type->isBackedEnum(); // true when Status is a backed enum
$type->isUnitEnum();   // true for any enum
$type->isIterable();   // false
```

Other predicates include `isScalar()`, `isInterface()`, `isStringable()` and `isNullable()`. `isIterable()` returns true for `array`, `iterable`, `Generator` and any `Iterator`.

## Comparing types

`matches()` checks whether the type is the same as, or a subtype of, a given class or interface name. `equals()` compares two type definitions for exact equality.

```php
$type = new TypeReflector(User::class);

$type->matches(JsonSerializable::class);       // true when User implements it
$type->equals('Full\Qualified\User');          // true
$type->equals(new TypeReflector(User::class)); // true
```

When the type names a class, `class()` returns a [`ClassReflector`](/reflection/api/ClassReflector) so you can keep navigating.

```php
$reflector = $type->class(); // ClassReflector
```

## Splitting unions and intersections

`split()` breaks a union or intersection type into its individual `TypeReflector` parts, which is handy when you want to validate against each member.

```php
$type = new TypeReflector('int|string|null');

foreach ($type->split() as $part) {
    echo $part->getName() . "\n"; // int, string, null
}
```
