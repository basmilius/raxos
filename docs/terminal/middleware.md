---
outline: deep
---

# Middleware

Middleware wraps the execution of a command. Each middleware runs before the command (or before the next middleware) and decides whether the chain continues. Guards such as confirmation prompts and environment checks are implemented this way.

## The middleware contract

A middleware implements `Raxos\Contract\Terminal\MiddlewareInterface`, which requires a single `handle()` method. It is also a class attribute, so it is placed above the command it guards.

```php
public function handle(
    CommandInterface $command,
    TerminalInterface $terminal,
    Printer $printer,
    Closure $next
): void;
```

The middleware calls `$next()` to continue the chain. Not calling it stops execution before the command runs, which is how a guard aborts.

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Middleware;

use Attribute;
use Closure;
use Raxos\Contract\Terminal\{CommandInterface, MiddlewareInterface, TerminalInterface};
use Raxos\Terminal\Printer;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class RequireRoot implements MiddlewareInterface
{
    public function handle(CommandInterface $command, TerminalInterface $terminal, Printer $printer, Closure $next): void
    {
        if (posix_getuid() !== 0) {
            $printer->incorrect('This command must be run as root.');
            $terminal->exit(-1);
        }

        $next();
    }
}
```

## Attaching middleware

Stack one or more middleware attributes above the command class. They run in the order they are declared.

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use App\Terminal\Middleware\RequireRoot;
use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\Command;
use Raxos\Terminal\Middleware\Confirm;
use Raxos\Terminal\Printer;

#[Command(name: 'cache:clear', description: 'Clears the application cache.')]
#[Confirm(message: 'This will clear the cache. Continue?')]
#[RequireRoot]
final readonly class CacheClearCommand implements CommandInterface
{
    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $printer->correct('Cache cleared.');
    }
}
```

## Built-in middleware

### Confirm

`Raxos\Terminal\Middleware\Confirm` asks the user to confirm before the command runs. It adds a `--force` option automatically; passing it skips the prompt. When the user declines, the terminal exits with code `-1`.

```php
#[Confirm(message: 'This will delete all records. Continue?')]
```

### Caution

`Raxos\Terminal\Middleware\Caution` only prompts when the `MODE` environment variable equals `production`. In any other environment it continues immediately, which makes it a lightweight guard for commands that are risky in production but harmless locally.

```php
#[Caution]
```

### Environment

`Raxos\Terminal\Middleware\Environment` restricts a command to a single environment. It continues when `MODE` matches the configured environment (`production` by default) and otherwise prints an error and exits with code `-1`.

```php
#[Environment(environment: 'staging')]
```

The `Caution` and `Environment` middleware read the current mode through the `env()` helper from [foundation](/foundation/).

## Options on middleware

A middleware may declare public properties marked with `#[Option]`, exactly like command constructor parameters. The terminal injects the parsed option values into those properties before `handle()` runs, and the help command lists them alongside the command's own options.

The built-in `Confirm` middleware uses this to expose its `--force` option:

```php
#[Option(description: 'Force the operation, bypassing confirmations and safety checks.', default: false)]
public bool $force;
```

Because a `readonly` property has no PHP default value, the `#[Option]` `default` argument supplies the fallback used when the option is absent from the command line.
