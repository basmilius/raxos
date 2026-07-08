---
outline: deep
---

# Commands

A command is the unit of work in a terminal application. It is any class that implements `Raxos\Contract\Terminal\CommandInterface` and carries a `#[Command]` attribute that gives it a name.

## Defining a command

The `#[Command]` attribute names the command and, optionally, describes it and documents its usage. `CommandInterface` requires a single `execute()` method that receives the running terminal and its printer.

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\Command;
use Raxos\Terminal\Printer;

#[Command(
    name: 'app:version',
    description: 'Prints the application version.'
)]
final readonly class VersionCommand implements CommandInterface
{
    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $printer->out('1.0.0');
    }
}
```

The `name` is the string typed on the command line to invoke the command. Names may contain colons and dashes, so `app:version` or `cache:clear` are valid.

## Arguments and options

Constructor parameters describe the input a command accepts. Mark a parameter with `#[Argument]` to make it a positional argument, or with `#[Option]` to make it a named `--option`.

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\{Argument, Command, Option};
use Raxos\Terminal\Printer;

#[Command(
    name: 'user:create',
    description: 'Creates a new user.',
    usage: 'user:create Bas --admin'
)]
final readonly class CreateUserCommand implements CommandInterface
{
    public function __construct(
        #[Argument(description: 'The name of the user.')]
        public string $name,

        #[Option(description: 'Grant the user administrator rights.')]
        public bool $admin = false
    ) {}

    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $printer->correct("Created {$this->name}.");
    }
}
```

Argument and option values are cast to the parameter type. An `int`, `float`, `bool` or `string` parameter receives a value of that type, so `--admin` reads as a boolean and a numeric argument reads as an integer or float.

::: warning
An argument may never be declared after an option. Put every `#[Argument]` parameter before the first `#[Option]` parameter, otherwise `Terminal::register()` throws an `InvalidCommandException`.
:::

### Optional arguments and options

A parameter is optional when it has a default value or a nullable type. Required parameters that receive no value cause a `MissingArgumentException` or `MissingOptionException` during parsing. On the command line an option can be written as `--name`, `--name=value` or `--name value`.

## Container dependencies

A constructor parameter that carries neither `#[Argument]` nor `#[Option]` and is typed with a class or interface is resolved from the [container](/container/) passed to the terminal. This lets a command request services directly.

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use App\Service\UserService;
use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\{Argument, Command};
use Raxos\Terminal\Printer;

#[Command(name: 'user:delete', description: 'Deletes a user.')]
final readonly class DeleteUserCommand implements CommandInterface
{
    public function __construct(
        #[Argument(description: 'The id of the user.')]
        public int $id,

        public UserService $users
    ) {}

    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $this->users->delete($this->id);
        $printer->correct('User deleted.');
    }
}
```

Instantiating a command with dependencies requires a container. Construct the terminal with one, otherwise the terminal throws an `InvalidCommandException` explaining that a container is required.

```php
<?php
declare(strict_types=1);

use App\Terminal\Command\DeleteUserCommand;
use Raxos\Container\Container;
use Raxos\Terminal\{Printer, Terminal};

$container = new Container(production: true);

$terminal = new Terminal(new Printer(), $container);
$terminal->register(DeleteUserCommand::class);
$terminal->execute();
```

::: info
A parameter without an attribute must be typed with a class or interface. A parameter that has no type at all, or only scalar types, produces an `InvalidCommandException` because there is nothing to resolve.
:::

## Registering and running

`Terminal::register()` adds a command class under the name from its `#[Command]` attribute and returns the terminal, so calls can be chained. It throws a `DuplicateCommandException` when the name is already taken and an `InvalidCommandException` when the class does not implement `CommandInterface` or is misconfigured.

```php
<?php
declare(strict_types=1);

use App\Terminal\Command\{CreateUserCommand, DeleteUserCommand, VersionCommand};
use Raxos\Terminal\Terminal;

$terminal = new Terminal();
$terminal
    ->register(VersionCommand::class)
    ->register(CreateUserCommand::class)
    ->register(DeleteUserCommand::class);

$terminal->execute();
```

`Terminal::execute()` parses the process arguments, finds the command by name and runs it. When no command name is given, it falls back to the built-in help command.

## The built-in help command

Every terminal automatically registers a `help` command. Running the terminal without arguments, or running `help`, prints a table of all registered commands with their descriptions. Passing a command name, as in `help user:create`, prints the detailed usage, arguments, options and middleware for that one command.

The help command is defined by `Raxos\Terminal\Command\HelpCommand`. It sorts and filters the registered commands using an [ArrayList](/collection/) and is also shown automatically after a command parsing error.

### The --extended help flag

Besides its optional command argument, the help command also accepts an `--extended` option. Without it, `help` prints the one line summary table where each command shows only its name and description. With it, `help --extended` prints the full detailed view for every registered command, listing the arguments, options and middleware just as `help user:create` would for a single command.

```shell
# Summary table of every command.
php console help

# Detailed view of every command.
php console help --extended

# Detailed view of one command (the flag is not needed here).
php console help user:create
```

Passing a specific command name always shows the detailed view, so `--extended` has no additional effect when a name is given. The flag only matters when listing every command at once.
