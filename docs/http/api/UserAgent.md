---
outline: deep
---

# UserAgent

`Raxos\Http\UserAgent`

Parses a `User-Agent` header string into a browser, platform and version. It is immutable and implements `JsonSerializable` and `Stringable`.

```php
readonly class UserAgent implements JsonSerializable, Stringable
```

## Constructor

```php
public function __construct(protected string $userAgent)
```

Parses the given user agent string. The parsed pieces are available as the public `readonly` properties `browser`, `platform` and `version`, each of which may be `null` when it could not be determined.

`HttpRequest::userAgent()` builds this object for you from the request header, so you rarely construct it by hand.

## Methods

```php
public function isChrome(): bool
```

Returns `true` when the browser is Google Chrome.

```php
public function isFirefox(): bool
```

Returns `true` when the browser is Mozilla Firefox.

```php
public function isSafari(): bool
```

Returns `true` when the browser is Apple Safari.

```php
public function isInternetExplorer(): bool
```

Returns `true` when the browser is Microsoft Internet Explorer.

```php
public function isMicrosoftEdge(): bool
```

Returns `true` when the browser is Microsoft Edge.

```php
public function versionAtLeast(string $version): bool
```

Returns `true` when the parsed browser version is at least the given version.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;

$request = HttpRequest::createFromGlobals();
$agent = $request->userAgent();

if ($agent !== null && $agent->isChrome() && $agent->versionAtLeast('120')) {
    // serve the modern bundle
}
```
