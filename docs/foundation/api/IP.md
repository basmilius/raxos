---
outline: deep
---

# IP

`Raxos\Foundation\Network\IP` is a value object representing a validated IPv4 or IPv6 address. It is a `final readonly class` implementing `JsonSerializable` and `Stringable`.

See the [Network concept page](/foundation/network) for a guided introduction.

## Signature

```php
namespace Raxos\Foundation\Network;

final readonly class IP implements JsonSerializable, Stringable
{
    public function __construct(
        public string $value,
        public IPVersion $version
    ) {}
}
```

## Methods

```php
public function __construct(string $value, IPVersion $version)
```
Creates an `IP` instance directly from a known value and version.

```php
public static function isValid(string $ip): bool
```
Returns true if the string is a valid IPv4 or IPv6 address.

```php
public static function isV4(string $ip): bool
```
Returns true if the string is a valid IPv4 address.

```php
public static function isV6(string $ip): bool
```
Returns true if the string is a valid IPv6 address.

```php
public static function parse(string $ip): ?self
```
Validates and parses a string into an `IP` instance, caching results (up to 1000 entries), or returns `null` if the string is invalid.

```php
public function jsonSerialize(): string
```
Returns the string value, so the instance serialises to its address in JSON.

```php
public function __toString(): string
```
Returns the string value.

## IPVersion

```php
namespace Raxos\Foundation\Network;

enum IPVersion: string
{
    case V4 = 'IPv4';
    case V6 = 'IPv6';
}
```

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Foundation\Network\IP;
use Raxos\Foundation\Network\IPVersion;

$ip = IP::parse('2001:db8::1');

if ($ip !== null && $ip->version === IPVersion::V6) {
    echo (string) $ip; // '2001:db8::1'
}
```
