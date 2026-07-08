---
outline: deep
---

# Printer

`Raxos\Terminal\Printer` is the output and prompt helper for terminal commands. It extends [league/climate](https://climate.thephpleague.com)'s `CLImate` with Raxos conventions such as `correct()` and `incorrect()` status lines.

```php
final class Printer extends CLImate
```

## Constants

- `Printer::CORRECT` : the check mark glyph used by `correct()`.
- `Printer::INCORRECT` : the cross mark glyph used by `incorrect()`.

## Methods added by Raxos

### `correct()`

```php
public function correct(string $message): self
```

Prints a green line prefixed with a check mark.

### `incorrect()`

```php
public function incorrect(string $message): self
```

Prints an error line on the error stream, prefixed with a cross mark.

### `to()`

```php
public function to(string $writer): self
```

Switches the active writer for subsequent calls, restricted to `'error'` or `'out'`.

## Inherited CLImate methods

All CLImate output and prompt methods are available. The most used ones include:

- Colors and styles: `green()`, `red()`, `yellow()`, `cyan()`, `bold()`, `underline()` and the other color methods, which can be chained.
- Structured output: `out()`, `table()`, `json()`, `dump()`, `columns()`, `border()`.
- Prompts: `confirm()`, `input()`, `password()`, `radio()`, `checkboxes()`.
- Progress: `progress()`, `spinner()`.

### `confirm()`

```php
public function confirm(string $prompt): Confirm
```

Asks a yes/no question and returns a `Confirm` prompt object.

### `checkboxes()`

```php
public function checkboxes(string $prompt, array $options, array $selected = []): Checkboxes
```

Uses the Raxos `Checkboxes` terminal object, which supports a list of pre-selected values.

### `table()`

```php
public function table(array $data): self
```

Renders an array of rows as a table.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Terminal\Printer;

$printer = new Printer();

$printer->correct('Everything is fine.');
$printer->to('error')->incorrect('Something is wrong.');

$printer->table([
    ['name' => 'Bas', 'role' => 'admin'],
]);

$selected = $printer
    ->checkboxes('Enable features:', ['cache', 'queue'], ['cache'])
    ->prompt();
```

For the complete list of colors, styles and prompt objects, see the [CLImate documentation](https://climate.thephpleague.com).
