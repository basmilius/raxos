---
outline: deep
---

# Autowiring and attributes

When you ask the container for a class that has no explicit binding, it autowires the class: it reflects the constructor, resolves each parameter, and builds the instance. Property injection and lazy proxies extend this with a small set of attributes. Autowiring is built on the reflectors from [Reflection](/reflection/).

## Constructor autowiring

A class without a binding is instantiated by resolving each constructor parameter through the container. Class typed parameters are resolved recursively, so a whole graph of dependencies is built from a single `get` call:

```php
<?php
declare(strict_types=1);

use Raxos\Container\Container;

final readonly class ReportService
{
    public function __construct(
        public Database $database,
        public LoggerInterface $logger,
    ) {}
}

$container = new Container();
$service = $container->get(ReportService::class);
```

A class with no constructor is instantiated without arguments. A class that is not instantiable (an interface or abstract class with no binding) raises a [`DependencyCannotInstantiateException`](/container/errors).

### Union types, defaults and nullable parameters

For a union typed parameter, the container tries each type in order and uses the first one that resolves. If none resolve, it falls back to the parameter default value when there is one:

```php
public function __construct(
    public CacheInterface|NullCache $cache = new NullCache(),
) {}
```

For built-in typed parameters (`int`, `string`, and so on) the container first checks whether a matching binding exists, then falls back to the default value. Variadic and iterable parameters default to an empty array, and optional parameters default to `null`. When nothing can be resolved and there is no fallback, a [`DependencyCannotAutowireException`](/container/errors) is raised.

## Property injection with #[Inject]

`#[Inject]` marks a property that is resolved and assigned right after the object is constructed. Only uninitialized properties are injected, so a value set in the constructor is left untouched. The attribute takes an optional `tag`:

```php
<?php
declare(strict_types=1);

use Raxos\Container\Attribute\Inject;

final class ReportBuilder
{
    #[Inject]
    public LoggerInterface $logger;

    #[Inject(tag: 'read')]
    public DatabaseConnection $connection;
}
```

## Lazy resolution with #[Proxy]

`#[Proxy]` defers resolution of a parameter or property until it is first used, by placing a lazy proxy object in its slot. This is useful for heavy dependencies that are not always needed, and for breaking otherwise circular graphs. It can be combined with `#[Inject]` on a property:

```php
<?php
declare(strict_types=1);

use Raxos\Container\Attribute\{Inject, Proxy};

final class MailQueue
{
    #[Inject]
    #[Proxy]
    public MailerInterface $mailer;

    public function __construct(
        #[Proxy] public readonly TemplateRenderer $renderer,
    ) {}
}
```

The proxied dependency is only built the moment a member of `$mailer` or `$renderer` is accessed.

## Scoping with #[Tag]

`#[Tag]` on a class or a constructor parameter selects a tagged variant of an abstract, matching the tags you register with `singleton`, `instance` or `get`. The name may be a string or a `UnitEnum`:

```php
<?php
declare(strict_types=1);

use Raxos\Container\Attribute\Tag;

final readonly class ReplicationService
{
    public function __construct(
        #[Tag('read')] public DatabaseConnection $reader,
        #[Tag('write')] public DatabaseConnection $writer,
    ) {}
}
```

## Caching an autowired class with #[Singleton]

`#[Singleton]` on a class tells the container to cache the first instance it autowires and reuse it for every later resolution, without an explicit `singleton` binding. It takes an optional `tag` under which the cached instance is stored:

```php
<?php
declare(strict_types=1);

use Raxos\Container\Attribute\Singleton;

#[Singleton]
final class Configuration
{
    // Built once, reused everywhere.
}
```

## Reading configuration with #[Env]

`#[Env]` resolves a built-in typed parameter or property from an environment variable, read through the `env()` helper from [Foundation](/foundation/). The raw value is coerced to match the declared type:

- `int` and `float` parameters are cast to numbers.
- `bool` parameters are `true` for `1`, `true`, `yes` or `on` (case insensitive) and `false` otherwise.
- any other type keeps the string value.

When the variable is not set, the container falls back to the parameter default value, then to `null` for nullable types, and otherwise raises a [`DependencyCannotAutowireException`](/container/errors).

```php
<?php
declare(strict_types=1);

use Raxos\Container\Attribute\Env;

final readonly class MailConfig
{
    public function __construct(
        #[Env('MAIL_HOST')] public string $host,
        #[Env('MAIL_PORT')] public int $port = 587,
        #[Env('MAIL_TLS')] public bool $tls = true,
    ) {}
}
```
