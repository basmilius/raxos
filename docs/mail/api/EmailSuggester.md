---
outline: deep
---

# EmailSuggester

`Raxos\Mail\EmailSuggester` proposes corrected email addresses for likely typos in a domain or its suffix.

```php
final readonly class EmailSuggester
```

## Methods

### `for`

```php
public static function for(Email|string $email): ?array
```

Returns an array of alternative [`Email`](/mail/api/Email) suggestions, or `null` when the address already looks correct. It accepts either an `Email` instance or a raw string, which it parses first (so it can throw `EmailAddressExceptionInterface` from [raxos/contract](/contract/) on invalid input).

The check works on two levels:

- It compares the provider part of the domain against a fixed list of common providers (`gmail`, `hotmail`, `outlook`, `live`, `icloud` and `me`) using Levenshtein distance, catching a one character slip such as `gmial`.
- It validates the domain suffix against the public suffix list, and when the suffix is not recognized it proposes corrected suffixes.

Each returned `Email` keeps the original username and tag, changing only the domain.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\EmailSuggester;

$suggestions = EmailSuggester::for('jane@gmial.com');
// [Email('jane', 'gmail.com')]

$none = EmailSuggester::for('jane@gmail.com');
// null

if ($suggestions !== null) {
    $didYouMean = (string)$suggestions[0];
    // prompt the user with $didYouMean
}
```
