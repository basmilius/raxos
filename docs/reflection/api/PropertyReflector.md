---
outline: deep
---

# PropertyReflector

`Raxos\Reflection\PropertyReflector`

Reflects a single class property. It can read, write and unset the property on a given instance, report its visibility and modifiers, and introspect its type, including the element type declared in an `@var` doc comment. It uses the [`Attributable`](/reflection/api/Attributable) trait for attribute reading.

```php
final readonly class PropertyReflector implements ReflectorInterface
```

The class implements the `ReflectorInterface` contract from [raxos/contract](/contract/).

## Methods

### `__construct()`

```php
public function __construct(ReflectionProperty $property)
```

Creates a reflector from a native `ReflectionProperty`. You usually obtain one from a [`ClassReflector`](/reflection/api/ClassReflector).

### `accepts()`

```php
public function accepts(mixed $input): bool
```

Checks whether a value is valid for the property's declared type, delegating to [`TypeReflector`](/reflection/api/TypeReflector).

### `getValue()`

```php
public function getValue(object $instance, mixed $default = null): mixed
```

Reads the property value from an instance. When the value is `null` or the property is not yet accessible, the default is returned instead.

### `setValue()`

```php
public function setValue(object $instance, mixed $value): void
```

Sets the property value on an instance.

### `unsetValue()`

```php
public function unsetValue(object $instance): void
```

Unsets the property on an instance.

### `isInitialized()`

```php
public function isInitialized(object $instance): bool
```

Checks whether the property has been initialized on the instance.

### `getClass()`

```php
public function getClass(): ClassReflector
```

Returns a [`ClassReflector`](/reflection/api/ClassReflector) for the declaring class.

### `getType()`

```php
public function getType(): TypeReflector
```

Returns a [`TypeReflector`](/reflection/api/TypeReflector) for the property's declared type.

### `getName()`

```php
public function getName(): string
```

Returns the property name.

### `getDefaultValue()`

```php
public function getDefaultValue(): mixed
```

Returns the declared default value.

### `hasDefaultValue()`

```php
public function hasDefaultValue(): bool
```

Checks whether the property has a default value. For promoted properties, it also inspects the matching constructor parameter.

### `hasType()`

```php
public function hasType(): bool
```

Checks whether the property has a declared type.

### `isIterable()`

```php
public function isIterable(): bool
```

Checks whether the property's type is iterable.

### `isPromoted()`

```php
public function isPromoted(): bool
```

Checks whether the property is a promoted constructor parameter.

### `isNullable()`

```php
public function isNullable(): bool
```

Checks whether the property's type allows `null`.

### `isPublic()`

```php
public function isPublic(): bool
```

Checks whether the property is public.

### `isProtected()`

```php
public function isProtected(): bool
```

Checks whether the property is protected.

### `isPrivate()`

```php
public function isPrivate(): bool
```

Checks whether the property is private.

### `isReadonly()`

```php
public function isReadonly(): bool
```

Checks whether the property is readonly.

### `isVirtual()`

```php
public function isVirtual(): bool
```

Checks whether the property is virtual.

### `getIterableType()`

```php
public function getIterableType(): ?TypeReflector
```

Reads the element type from an `@var Type[]` style doc comment, if present, and returns it as a [`TypeReflector`](/reflection/api/TypeReflector).

## Usage

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$property = reflect(User::class)->getProperty('name');

if (!$property->isInitialized($user)) {
    $property->setValue($user, 'Bas');
}

echo $property->getValue($user);
```
