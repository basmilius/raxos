---
outline: deep
---

# Attachment

`Raxos\Mail\Attachment` is a named piece of raw content attached to a [`Mail`](/mail/api/Mail). It does no filesystem access of its own: you supply the bytes, whether you read them from disk or generated them in memory.

```php
final readonly class Attachment
```

## Constructor

```php
public function __construct(
    public string $name,
    public string $content
)
```

`$name` is the file name shown to the recipient and `$content` is the raw content. Reading a file is the caller's responsibility.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Mail\Attachment;

$fromDisk = new Attachment('invoice.pdf', file_get_contents('/path/to/invoice.pdf'));
$fromMemory = new Attachment('report.csv', "id,name\n1,Jane\n");
```
