---
outline: deep
---

# Reading attributes

Every reflector class in the package uses the [`Attributable`](/reflection/api/Attributable) trait, so reading PHP attributes works exactly the same way on a class, method, property, parameter or function. This is the mechanism the rest of Raxos relies on for attribute driven behavior, from ORM column mapping to router route discovery.

## Instantiated attributes

`getAttribute()` returns a single instantiated attribute, or `null` when it is absent. `getAttributes()` returns every matching instance as an array, including subclasses of the requested attribute.

```php
<?php
declare(strict_types=1);

use function Raxos\Reflection\reflect;

$reflector = reflect(User::class);

$table = $reflector->getAttribute(Table::class);   // ?Table
$columns = $reflector->getProperty('id')->getAttributes(Column::class); // Column[]
```

## Raw attributes

When you only need the raw `ReflectionAttribute` (for example to inspect its arguments without constructing it), use `getRawAttribute()` and `getRawAttributes()`. These never call `newInstance()`.

```php
$raw = $reflector->getRawAttribute(Table::class); // ?ReflectionAttribute

foreach ($reflector->getRawAttributes(Column::class) as $attribute) {
    // $attribute->getArguments()
}
```

## Checking for presence

`hasAttribute()` reports whether an attribute is present without instantiating it. Pass `instanceOf: true` to also match subclasses of the given attribute.

```php
if ($reflector->hasAttribute(Table::class)) {
    // ...
}

if ($reflector->hasAttribute(Relation::class, instanceOf: true)) {
    // matches HasMany, BelongsTo and any other Relation subclass
}
```

## Recursive lookups

On a `ClassReflector`, `getAttribute()` accepts a `recursive` flag. When set and the attribute is not found on the class itself, the lookup walks up the implemented interfaces and then the parent class chain until it finds a match.

```php
$attribute = $reflector->getAttribute(Serializable::class, recursive: true);
```

::: info
The recursive walk only applies to `ClassReflector`. On the other reflectors the `recursive` argument has no effect, since methods, properties and parameters do not have a class style hierarchy of their own.
:::
