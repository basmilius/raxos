---
outline: deep
---

# #[Option]

`Raxos\Terminal\Attribute\Option` marks a command constructor parameter or a middleware property as a named `--option`.

```php
#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final readonly class Option implements AttributeInterface
```

## Constructor

```php
public function __construct(
    ?string $name = null,
    ?string $description = null,
    ?string $example = null,
    mixed $default = None::class
)
```

- `$name` : optionally overrides the option name used on the command line. Defaults to the parameter or property name.
- `$description` : documents the option in the help output.
- `$example` : an optional example value.
- `$default` : a default value that is used when the parameter or property has no PHP default of its own. This is useful on a middleware whose `readonly` property cannot carry a default.

## Behavior

- Works on both command constructor parameters and public middleware properties.
- Option values are read from `--name`, `--name=value` or `--name value` on the command line and cast to the target type.
- An option is optional when it has a default (from PHP or from the `default` argument) or a nullable type. A required option with no value produces a `MissingOptionException`.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Terminal\Command;

use Raxos\Contract\Terminal\{CommandInterface, TerminalInterface};
use Raxos\Terminal\Attribute\{Command, Option};
use Raxos\Terminal\Printer;

#[Command(name: 'report:build', description: 'Builds a report.')]
final readonly class BuildReportCommand implements CommandInterface
{
    public function __construct(
        #[Option(name: 'format', description: 'The output format.')]
        public string $format = 'pdf'
    ) {}

    public function execute(TerminalInterface $terminal, Printer $printer): void
    {
        $printer->correct("Building a {$this->format} report.");
    }
}
```

On a middleware, the `default` argument supplies the value when the option is absent, as the built-in `Confirm` middleware does with its `--force` option. See [Middleware](/terminal/middleware) for that pattern.
