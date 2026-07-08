---
outline: deep
---

# Singleton

`Raxos\Foundation\Util\Singleton` is a static registry that keeps at most one instance per class name. It backs the global `singleton()` function and `Option::none()`.

See the [Singleton concept page](/foundation/singleton-and-stopwatch) for a guided introduction.

## Signature

```php
namespace Raxos\Foundation\Util;

final class Singleton
```

## Methods

```php
public static function get(string $class): object
```
Returns the existing instance, or creates one with a plain constructor call and stores it.

```php
public static function getOrNull(string $class): ?object
```
Returns the existing instance, or `null` if none was created yet.

```php
public static function has(string $class): bool
```
Returns true if an instance for the class already exists.

```php
public static function make(string $class): object
```
Forces creation of a new instance with a plain constructor call and stores it.

```php
public static function register(string $class, callable $setup): object
```
Returns the existing instance, or creates one using the given factory callable.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Util\Singleton;

$service = Singleton::get(MyService::class);

$configured = Singleton::register(
    MyService::class,
    static fn() => new MyService($config)
);
```
