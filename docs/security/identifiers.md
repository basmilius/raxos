---
outline: deep
---

# Identifiers

The `Raxos\Security\Id` namespace provides two identifier generators: [`NanoId`](/security/api/NanoId) for short random identifiers and [`Ulid`](/security/api/Ulid) for identifiers that sort by creation time. Pick NanoId when you want a compact, opaque handle, and pick Ulid when you want identifiers that order chronologically and carry a timestamp.

## NanoId

`NanoId::generate` produces a short, URL friendly, random identifier. The default length is 16 characters, drawn from an alphabet of digits, letters, `_` and `-`.

```php
<?php
declare(strict_types=1);

use Raxos\Security\Id\NanoId;

$id = NanoId::generate();      // 16 characters
$short = NanoId::generate(12); // 12 characters
```

## ULID

A ULID is a 26 character identifier made of a 10 character time part and a 16 character randomness part. Because the time part comes first and uses a sortable encoding, ULIDs order lexicographically by the moment they were created. `Ulid` implements `Stringable`, so an instance can be cast to a string directly for storage or output.

```php
<?php
declare(strict_types=1);

use Raxos\Security\Id\Ulid;

$ulid = Ulid::generate();
$asString = (string)$ulid;
$createdAt = $ulid->toTimestamp(); // milliseconds since the Unix epoch
```

Pass `lowercase: true` to any of the factory methods to emit the identifier in lower case.

### Building a ULID for a specific time

`Ulid::fromTimestamp` builds an identifier for a given millisecond timestamp. Repeated calls within the same millisecond use monotonic randomness, so the identifiers still sort in the order they were created.

```php
$ulid = Ulid::fromTimestamp(1704067200000);
```

### Parsing an existing ULID

`Ulid::fromString` parses an existing identifier and validates both its length and its character set. It throws when the value is the wrong length (`UlidInvalidLengthException`) or contains characters outside the ULID alphabet (`UlidWrongCharactersException`). Both implement `UlidExceptionInterface` from [raxos/contract](/contract/).

```php
$ulid = Ulid::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
$timestamp = $ulid->toTimestamp();
```
