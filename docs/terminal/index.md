---
outline: deep

cards:
    highlights:
        -   title: Terminal
            code: true
            details: 'The application that parses process arguments, resolves a command and runs it.'
            link: /terminal/api/Terminal
        -   title: Printer
            code: true
            details: 'Output and prompt helper built on CLImate, with correct() and incorrect() status lines.'
            link: /terminal/api/Printer
        -   title: '#[Command]'
            code: true
            details: 'The class attribute that turns a class into a runnable command.'
            link: /terminal/api/Command
        -   title: Middleware
            code: true
            details: 'Guards such as Confirm, Caution and Environment that wrap a command before it runs.'
            link: /terminal/middleware
---

# Terminal

Raxos Terminal provides a small, attribute-driven framework for building custom command line tools on top of [league/climate](https://climate.thephpleague.com). A command is a plain class tagged with a `#[Command]` attribute and implementing `CommandInterface`. Its constructor parameters become positional arguments (`#[Argument]`) or named options (`#[Option]`), and can also be resolved from a [container](/container/) service. A `Terminal` instance parses the process arguments, looks up the matching registered command, runs it through an optional chain of middleware, and prints output and prompts through a `Printer`. A built-in help command documents every registered command automatically, and uncaught exceptions are rendered as readable Collision error reports.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Commands](/terminal/commands): define, register and run commands with the `#[Command]`, `#[Argument]` and `#[Option]` attributes.
- [Middleware](/terminal/middleware): the pipeline that wraps a command and the built-in `Confirm`, `Caution` and `Environment` guards.
- [The Printer](/terminal/printer): all terminal output and interactive prompts.
- [Errors and reporting](/terminal/errors): the exceptions the package throws and how uncaught errors are reported.

## Quick example

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\{Argument, Command, Option};
use Raxos\Terminal\Printer;

#[Command(
    name: 'greet',
    description: 'Greets a person.',
    usage: 'greet Bas --shout'
)]
final readonly class GreetCommand implements CommandInterface
{
    public function __construct(
        #[Argument(description: 'The name of the person to greet.')]
        public string $name,

        #[Option(description: 'Print the greeting in capitals.')]
        public bool $shout = false
    ) {}

    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $message = "Hello, {$this->name}!";

        if ($this->shout) {
            $message = strtoupper($message);
        }

        $printer->correct($message);
    }
}
```

```php
<?php
declare(strict_types=1);

use App\Terminal\Command\GreetCommand;
use Raxos\Terminal\Terminal;

$terminal = new Terminal();
$terminal->register(GreetCommand::class);
$terminal->execute();
```

## Installation

See [Installation](/terminal/installation) for the Composer command and the runtime requirements.
