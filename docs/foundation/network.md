---
outline: deep
---

# Network: IP

The `Raxos\Foundation\Network\IP` class is a small value object that validates, parses and represents an IPv4 or IPv6 address. It is a `final readonly class` that implements `JsonSerializable` and `Stringable`, so an `IP` serialises to its string form in JSON and can be used directly where a string is expected.

## Validating

The static validators wrap PHP's `filter_var` and return a boolean:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Network\IP;

IP::isValid('203.0.113.10');   // true
IP::isValid('2001:db8::1');    // true
IP::isValid('not-an-ip');      // false

IP::isV4('203.0.113.10');      // true
IP::isV6('203.0.113.10');      // false
IP::isV6('2001:db8::1');       // true
```

## Parsing

`IP::parse()` validates the string and returns an `IP` instance, or `null` if the string is not a valid address. Results are cached internally (up to 1000 entries) so repeated lookups of the same address are cheap.

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Network\IP;
use Raxos\Foundation\Network\IPVersion;

$ip = IP::parse('2001:db8::1');

if ($ip !== null) {
    $ip->value;                          // '2001:db8::1'
    $ip->version === IPVersion::V6;      // true
    (string) $ip;                        // '2001:db8::1'
    json_encode($ip);                    // '"2001:db8::1"'
}
```

## Constructing directly

If you already know the value and version, you can construct an `IP` directly. Both properties are public and readonly:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Network\IP;
use Raxos\Foundation\Network\IPVersion;

$ip = new IP('203.0.113.10', IPVersion::V4);
```

## IPVersion

`IPVersion` is a string backed enum with two cases:

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Network\IPVersion;

IPVersion::V4->value; // 'IPv4'
IPVersion::V6->value; // 'IPv6'
```

See the [IP API reference](/foundation/api/IP) for full method signatures.
