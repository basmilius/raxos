---
outline: deep
---

# The Printer

The `Printer` handles all terminal output and interactive prompts. It extends [league/climate](https://climate.thephpleague.com)'s `CLImate` class, so every CLImate color, style and dynamic prompt is available, plus a few Raxos conventions on top.

A single `Printer` instance is created for each `Terminal` and passed into every command and middleware call, so commands never construct their own printer.

## Status lines

`correct()` and `incorrect()` print quick pass and fail feedback. `correct()` writes a green line prefixed with a check mark, and `incorrect()` writes an error line prefixed with a cross mark.

```php
$printer->correct('The migration finished.');
$printer->incorrect('The migration failed.');
```

## Choosing the output stream

`to()` switches the active writer for subsequent calls. It is restricted to `'error'` and `'out'`.

```php
$printer->to('error')->out('Written to stderr.');
$printer->to('out')->out('Written to stdout.');
```

## Colors, styles and structured output

Because `Printer` extends `CLImate`, all of its output helpers work directly. Colors and styles can be chained.

```php
$printer->bold()->green('Done.');
$printer->yellow('A warning.');

$printer->table([
    ['name' => 'Bas', 'role' => 'admin'],
    ['name' => 'Alex', 'role' => 'user'],
]);

$printer->json(['status' => 'ok']);
```

## Interactive prompts

The dynamic CLImate prompts are available for reading input from the user.

```php
$confirm = $printer->confirm('Do you want to continue?');

if ($confirm->confirmed()) {
    $name = $printer->input('What is your name?')->prompt();
    $secret = $printer->password('Enter a password:')->prompt();
    $role = $printer->radio('Pick a role:', ['admin', 'user'])->prompt();
}
```

### Checkboxes

`checkboxes()` uses a Raxos-flavored `Checkboxes` prompt that supports a list of pre-selected values. Pass the already selected options as the third argument.

```php
$selected = $printer
    ->checkboxes('Select the features to enable:', ['cache', 'queue', 'search'], ['cache'])
    ->prompt();
```

## Progress and spinners

Long-running work can report progress with a bar or a spinner.

```php
$progress = $printer->progress(100);

for ($step = 0; $step <= 100; $step++) {
    $progress->current($step);
}
```

For the full list of colors, styles and prompt objects, see the [CLImate documentation](https://climate.thephpleague.com). The [Printer API reference](/terminal/api/Printer) lists the methods that Raxos adds or overrides.
