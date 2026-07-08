---
outline: deep
---

# Attributes

`Raxos\Container\Attribute`

The container ships a small set of attributes that steer autowiring. Each one implements `AttributeInterface` from [Contract](/contract/). See [Autowiring and attributes](/container/autowiring) for how they fit together.

## Env

```php
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final readonly class Env implements AttributeInterface
{
    public function __construct(
        public string $name,
    ) {}
}
```

Resolves a built-in typed parameter or property from an environment variable, read through the `env()` helper from [Foundation](/foundation/). The value is coerced to `int`, `float` or `bool` to match the declared type; any other type keeps the string value. Missing variables fall back to the default value, then to `null` for nullable types.

```php
public function __construct(
    #[Env('APP_DEBUG')] public bool $debug = false,
) {}
```

## Inject

```php
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final readonly class Inject implements AttributeInterface
{
    public function __construct(
        public UnitEnum|string|null $tag = null,
    ) {}
}
```

Marks a property to be resolved and injected right after the object is constructed, optionally scoped by a tag. Only uninitialized properties are injected.

```php
#[Inject(tag: 'read')]
public DatabaseConnection $connection;
```

## Proxy

```php
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final readonly class Proxy implements AttributeInterface {}
```

Marks a parameter or property so its dependency is resolved lazily behind a proxy object, instead of eagerly. The real instance is only built the moment the proxy is first used. It combines with `#[Inject]` on a property.

```php
public function __construct(
    #[Proxy] public readonly TemplateRenderer $renderer,
) {}
```

## Singleton

```php
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Singleton implements AttributeInterface
{
    public function __construct(
        public UnitEnum|string|null $tag = null,
    ) {}
}
```

Marks a class so the first instance the container autowires is cached and reused, without an explicit `singleton` binding. Optionally scoped by a tag.

```php
#[Singleton]
final class Configuration {}
```

## Tag

```php
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Tag implements AttributeInterface
{
    public function __construct(
        public UnitEnum|string $name,
    ) {}
}
```

Scopes a class binding, or a constructor parameter, to a named or enum backed variant of an abstract. It matches the tags you register with `singleton`, `instance` or `get`.

```php
public function __construct(
    #[Tag('write')] public DatabaseConnection $writer,
) {}
```
