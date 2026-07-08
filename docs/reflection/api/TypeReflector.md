---
outline: deep
---

# TypeReflector

`Raxos\Reflection\TypeReflector`

Represents and validates a resolved PHP type. It normalizes a native reflection type, a reflector or a plain type string into a single definition, then answers questions about it: what kind of type it is, whether it accepts a given value, and how it relates to other types. It handles builtins, classes, interfaces, enums, unions and intersections.

```php
final readonly class TypeReflector implements ReflectorInterface
```

The class implements the `ReflectorInterface` contract from [raxos/contract](/contract/).

## Properties

- `bool $isNullable`: whether the type allows `null`. It is also available through `isNullable()`.

## Methods

### `__construct()`

```php
public function __construct(Reflector|ReflectionType|string $type)
```

Creates a reflector from a native `ReflectionType`, a `ReflectionParameter`, a `ReflectionProperty`, a `ReflectionClass`, any other `Reflector`, or a plain type string.

### `accepts()`

```php
public function accepts(mixed $input): bool
```

Checks whether a runtime value matches the type, including builtins, classes, iterables, unions and intersections.

### `matches()`

```php
public function matches(string $type): bool
```

Checks whether the type is the same as, or a subtype of, the given class or interface name.

### `class()`

```php
public function class(): ClassReflector
```

Returns a [`ClassReflector`](/reflection/api/ClassReflector) for the type, when it names a class.

### `equals()`

```php
public function equals(string|self $type): bool
```

Checks whether two types have the same definition.

### `isBuiltIn()`

```php
public function isBuiltIn(): bool
```

Checks whether the type is a PHP builtin such as `string`, `int` or `array`.

### `isNullable()`

```php
public function isNullable(): bool
```

Checks whether the type allows `null`.

### `isScalar()`

```php
public function isScalar(): bool
```

Checks whether the type is `bool`, `float`, `int` or `string`.

### `isClass()`

```php
public function isClass(): bool
```

Checks whether the type names an existing class. Interfaces are not considered classes here.

### `isInterface()`

```php
public function isInterface(): bool
```

Checks whether the type names an existing interface.

### `isEnum()`

```php
public function isEnum(): bool
```

Checks whether the type is a unit or backed enum.

### `isBackedEnum()`

```php
public function isBackedEnum(): bool
```

Checks whether the type is a backed enum.

### `isUnitEnum()`

```php
public function isUnitEnum(): bool
```

Checks whether the type is a unit enum. This is true for any enum.

### `isIterable()`

```php
public function isIterable(): bool
```

Checks whether the type is iterable: `array`, `iterable`, `Generator` or any `Iterator`.

### `isStringable()`

```php
public function isStringable(): bool
```

Checks whether the type is a string or implements `Stringable`.

### `split()`

```php
public function split(): array
```

Splits a union or intersection type into its individual `TypeReflector` parts.

### `getName()`

```php
public function getName(): string
```

Returns the raw type definition.

### `getShortName()`

```php
public function getShortName(): string
```

Returns the type name without its namespace.

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\Reflection\TypeReflector;

$type = new TypeReflector('int|string');

$type->accepts(42);    // true
$type->accepts('foo'); // true
$type->accepts(3.14);  // false

foreach ($type->split() as $part) {
    echo $part->getName() . "\n"; // int, string
}
```
