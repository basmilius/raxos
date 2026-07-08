---
outline: deep
---

# Attributable

`Raxos\Reflection\Attributable`

The trait shared by every reflector in the package. It adds consistent PHP attribute reading to [`ClassReflector`](/reflection/api/ClassReflector), [`MethodReflector`](/reflection/api/MethodReflector), [`PropertyReflector`](/reflection/api/PropertyReflector), [`ParameterReflector`](/reflection/api/ParameterReflector) and [`FunctionReflector`](/reflection/api/FunctionReflector), so attribute access works the same way everywhere.

```php
trait Attributable
```

The trait holds the underlying native reflection object in a shared `$reflection` property, which is the object it reads attributes from.

## Methods

### `getAttribute()`

```php
public function getAttribute(string $name, bool $recursive = false): ?object
```

Returns an instantiated attribute, or `null` when it is absent. On a `ClassReflector`, passing `recursive: true` continues the search up the implemented interfaces and then the parent class chain.

### `getAttributes()`

```php
public function getAttributes(string $name): array
```

Returns all matching attribute instances, including subclasses of the given attribute.

### `getRawAttribute()`

```php
public function getRawAttribute(string $name): ?ReflectionAttribute
```

Returns the first matching attribute as a native `ReflectionAttribute`, without instantiating it.

### `getRawAttributes()`

```php
public function getRawAttributes(string $name): array
```

Returns all matching attributes as native `ReflectionAttribute` objects, without instantiating them.

### `hasAttribute()`

```php
public function hasAttribute(string $name, bool $instanceOf = false): bool
```

Checks whether the given attribute is present without instantiating it. Pass `instanceOf: true` to also match subclasses.

## Usage

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$reflector = reflect(User::class);

$table = $reflector->getAttribute(Table::class);

if ($reflector->hasAttribute(Table::class)) {
    // ...
}

foreach ($reflector->getRawAttributes(Column::class) as $attribute) {
    // $attribute->getArguments()
}
```

See the [Reading attributes](/reflection/attributes) concept page for a fuller walkthrough.
