---
outline: deep
---

# #[Command]

`Raxos\Terminal\Attribute\Command` is the class attribute that turns a class into a runnable command by giving it a name, description and usage example. It is placed on the command class, not on the constructor.

```php
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Command implements AttributeInterface
```

## Constructor

```php
public function __construct(
    string $name,
    ?string $description = null,
    ?string $usage = null
)
```

- `$name` : the string typed on the command line to invoke the command. Names may contain colons and dashes, such as `cache:clear`.
- `$description` : an optional one-line description shown by the built-in help command.
- `$usage` : an optional usage example, shown in the detailed help output.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\Command;
use Raxos\Terminal\Printer;

#[Command(
    name: 'cache:clear',
    description: 'Clears the application cache.',
    usage: 'cache:clear'
)]
final readonly class CacheClearCommand implements CommandInterface
{
    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $printer->correct('Cache cleared.');
    }
}
```

See [Commands](/terminal/commands) for how the attribute combines with `#[Argument]` and `#[Option]`.
