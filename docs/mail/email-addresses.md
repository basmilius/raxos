---
outline: deep
---

# Email addresses and suggestions

Beyond composing and sending, the package gives you two tools for working with the addresses themselves: [`Email`](/mail/api/Email) parses and validates an address, and [`EmailSuggester`](/mail/api/EmailSuggester) spots likely typos and proposes corrections.

## The Email value object

`Email` is an immutable value object split into three parts: a `username`, a `domain` and an optional plus `tag`. You can build one directly from its parts, but most of the time you parse a raw string with `Email::fromString`.

```php
<?php
declare(strict_types=1);

use Raxos\Mail\Email;

$email = Email::fromString('jane+newsletter@example.com');

$email->username; // 'jane'
$email->domain;   // 'example.com'
$email->tag;      // 'newsletter'
```

`fromString` validates the input with `FILTER_VALIDATE_EMAIL` and requires exactly one `@`. When the local part contains a `+`, everything after it becomes the `tag`. Anything that is not a single valid address throws an `InvalidEmailAddressException`, which implements `EmailAddressExceptionInterface` from [raxos/contract](/contract/).

```php
use Raxos\Contract\Mail\EmailAddressExceptionInterface;

try {
    $email = Email::fromString('not-an-address');
} catch (EmailAddressExceptionInterface $err) {
    // handle the invalid input
}
```

`Email` implements both `Stringable` and `JsonSerializable`. It renders back to `username@domain`, or `username+tag@domain` when a tag is present, in both string and JSON contexts.

```php
echo (string)Email::fromString('jane+news@example.com');
// jane+news@example.com

echo json_encode(['email' => Email::fromString('jane@example.com')]);
// {"email":"jane@example.com"}
```

## Suggesting corrections

`EmailSuggester::for` looks at an address and returns a list of likely intended addresses, or `null` when nothing looks wrong. It accepts either an `Email` instance or a raw string (which it parses for you, so it can also throw `EmailAddressExceptionInterface`).

```php
<?php
declare(strict_types=1);

use Raxos\Mail\EmailSuggester;

$suggestions = EmailSuggester::for('jane@gmial.com');
// [Email('jane', 'gmail.com')]

$none = EmailSuggester::for('jane@gmail.com');
// null
```

The suggester works on two levels. It compares the provider part of the domain against a fixed list of common providers (`gmail`, `hotmail`, `outlook`, `live`, `icloud` and `me`) using Levenshtein distance, so a one character slip like `gmial` is caught. It also validates the domain suffix against the public suffix list, and when the suffix itself is unrecognized it proposes corrected suffixes. Each entry in the returned array is a fresh `Email` that keeps the original username and tag.

Because the result is either an array of `Email` objects or `null`, a common pattern is to offer the first suggestion back to the user before sending.

```php
$suggestions = EmailSuggester::for($input);

if ($suggestions !== null) {
    $didYouMean = (string)$suggestions[0];
    // prompt the user with $didYouMean
}
```
