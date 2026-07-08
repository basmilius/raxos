---
outline: deep
---

# Calling callables

Besides building objects, the container can invoke a callable and autowire the arguments it does not receive explicitly. This is what a router or CLI framework uses to call a controller action or command handler: it passes the arguments it knows (route parameters, the request) and lets the container supply the rest from its bindings.

## The call method

`call` accepts three shapes of callable:

- a `Closure`,
- an array `[object|class-string, method]`,
- an invokable class string (a class with an `__invoke` method).

Each parameter of the callable is resolved the same way a constructor parameter is, using the autowiring rules from [Autowiring and attributes](/container/autowiring).

```php
<?php
declare(strict_types=1);

use Raxos\Container\Container;

$container = new Container();

$result = $container->call(static fn(Database $database): int =>
    $database->count('users'));
```

## Overriding arguments

Named arguments passed in `$args` take precedence over autowired values. They are matched by parameter name, so any parameter present in the array is taken from it and the rest are autowired:

```php
$container->call([$reportController, 'show'], [
    'id' => 42,
]);
```

Here `id` comes from the array, while a `Database` or `Request` parameter on `show` is resolved from the container.

## Invokable classes

A class string that implements `__invoke` is instantiated (and autowired) before being called:

```php
<?php
declare(strict_types=1);

final readonly class SendWelcomeMail
{
    public function __construct(
        public MailerInterface $mailer,
    ) {}

    public function __invoke(User $user): void
    {
        $this->mailer->send(/* ... */);
    }
}

$container->call(SendWelcomeMail::class, [
    'user' => $user,
]);
```

When the given value is not a closure, a valid `[target, method]` pair, or an invokable class string, `call` throws an [`InvalidCallableException`](/container/errors).
