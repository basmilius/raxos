---
outline: deep
---

# Errors and reporting

The terminal distinguishes between two kinds of failure: problems with the command line or a command's configuration, which are reported as a friendly message plus help, and unexpected exceptions thrown while a command runs, which are rendered as a full Collision report.

## The exceptions

Every exception in `Raxos\Terminal\Error` extends the base `Exception` class from [error](/error/) and implements `CommandExceptionInterface` from [contract](/contract/).

| Exception | Thrown when |
| --- | --- |
| `CommandNotFoundException` | The parsed command name matches no registered command. |
| `MissingArgumentException` | A required positional argument is missing from the command line. |
| `MissingOptionException` | A required option has no value and no default. |
| `InvalidCommandException` | A command class is misconfigured, for example it is missing its `#[Command]` attribute or has an untyped parameter. |
| `DuplicateCommandException` | `Terminal::register()` is given a name that is already registered. |
| `ReflectionErrorException` | Reflection fails while parsing a command or middleware class. |

`DuplicateCommandException` and `InvalidCommandException` surface at registration time. `CommandNotFoundException`, `MissingArgumentException` and `MissingOptionException` surface while parsing and instantiating a command in `Terminal::execute()`.

## How execute() handles failure

`Terminal::execute()` wraps command resolution and execution and reacts to what is thrown:

- An `InvalidArgumentException` prints its message through `Printer::incorrect()` and exits with code `-1`.
- Any `CommandExceptionInterface` prints its message through `Printer::incorrect()`, then shows contextual help for the attempted command, and exits with code `-2`.
- Any other `Throwable` is rendered as a full Collision report through `Raxos\Terminal\Collision\ErrorReporter` before exiting with code `9`.

This means a mistake on the command line gives the user an error and the relevant help output, while a genuine bug in a command produces a readable stack trace.

## Collision reports

Uncaught exceptions from a command's `execute()` method are passed to `ErrorReporter::exception()`, which uses [nunomaduro/collision](https://github.com/nunomaduro/collision) to print a formatted report with the message, the source location and a stack trace. No extra configuration is needed; any `Throwable` that escapes a command is reported this way.

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\Command;
use Raxos\Terminal\Printer;
use RuntimeException;

#[Command(name: 'app:boom', description: 'Always fails.')]
final readonly class BoomCommand implements CommandInterface
{
    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        throw new RuntimeException('Something went wrong.');
    }
}
```

Running `app:boom` prints a Collision report for the `RuntimeException` and exits with code `9`.
