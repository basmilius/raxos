---
outline: deep
---

# Terminal

`Raxos\Terminal\Terminal` is the base terminal application. It holds the registered commands, executes the parsed command line, and exposes the shared `Printer` and optional [container](/container/) to commands and middleware. It implements `Raxos\Contract\Terminal\TerminalInterface`.

```php
class Terminal implements TerminalInterface
```

## Properties

- `public readonly Printer $printer` : the printer shared by every command and middleware.
- `public readonly ?ContainerInterface $container` : the optional container used to resolve command dependencies.
- `public private(set) array $commands` : a read-only map of command name to class-string. It always contains the built-in `help` command.

## Methods

### `__construct()`

```php
public function __construct(
    Printer $printer = new Printer(),
    ?ContainerInterface $container = null
)
```

Creates a terminal with a `Printer` instance and an optional container. Provide a container when your commands request services through untyped constructor parameters.

### `register()`

```php
public function register(string $commandClass): static
```

Registers a command class under the name from its `#[Command]` attribute and returns the terminal for chaining. The class must implement `CommandInterface`. Throws `InvalidCommandException` when the class is invalid and `DuplicateCommandException` when the name is already taken.

### `execute()`

```php
public function execute(): void
```

Parses the process arguments, resolves the matching command, runs its middleware chain, and prints a friendly error (or a Collision report) when something goes wrong. Falls back to the built-in help command when no command name is given. See [Errors and reporting](/terminal/errors) for the exit codes.

### `exit()`

```php
public function exit(int $code = 0): never
```

Ends the process with the given exit code.

## Example

```php
<?php
declare(strict_types=1);

use App\Terminal\Command\{CreateUserCommand, VersionCommand};
use Raxos\Container\Container;
use Raxos\Terminal\{Printer, Terminal};

$container = new Container(production: true);

$terminal = new Terminal(new Printer(), $container);
$terminal
    ->register(VersionCommand::class)
    ->register(CreateUserCommand::class);

$terminal->execute();
```

A custom terminal can extend this class to pre-register a fixed set of commands. Subclasses rely on `execute()`, `exit()` and `register()` as their entry points.
