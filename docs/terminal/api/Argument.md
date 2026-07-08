---
outline: deep
---

# #[Argument]

`Raxos\Terminal\Attribute\Argument` marks a command constructor parameter as a positional argument.

```php
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class Argument implements AttributeInterface
```

## Constructor

```php
public function __construct(
    ?string $name = null,
    ?string $description = null,
    ?string $example = null
)
```

- `$name` : optionally overrides the parameter name shown on the command line and in help output. Defaults to the parameter name.
- `$description` : documents the argument in the detailed help output.
- `$example` : an optional example value.

## Behavior

- Placed on a constructor parameter, an argument must come before any `#[Option]` parameter, otherwise the command is rejected with an `InvalidCommandException`.
- A nullable type or a default value makes the argument optional. A required argument with no value produces a `MissingArgumentException`.
- The value is cast to the parameter type, so an `int` parameter receives an integer.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\{Argument, Command};
use Raxos\Terminal\Printer;

#[Command(name: 'user:show', description: 'Shows a user.')]
final readonly class ShowUserCommand implements CommandInterface
{
    public function __construct(
        #[Argument(description: 'The id of the user.')]
        public int $id
    ) {}

    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $printer->out("User #{$this->id}");
    }
}
```

See [Commands](/terminal/commands) for the full argument and option rules.
