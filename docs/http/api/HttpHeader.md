---
outline: deep
---

# HttpHeader

`Raxos\Http\HttpHeader`

A `final` class of string constants for well known HTTP header names. Using these constants instead of hardcoded strings turns a typo into a compile time error and keeps header naming consistent across the package.

```php
final class HttpHeader
```

## Constants

Every constant is a lowercase header name, matching the way `HttpHeadersMap` normalizes its keys. The class covers permanent, provisional and non-standard headers. A selection:

| Constant | Value |
| --- | --- |
| `HttpHeader::ACCEPT` | `accept` |
| `HttpHeader::ACCEPT_LANGUAGE` | `accept-language` |
| `HttpHeader::AUTHORIZATION` | `authorization` |
| `HttpHeader::CACHE_CONTROL` | `cache-control` |
| `HttpHeader::CONTENT_DISPOSITION` | `content-disposition` |
| `HttpHeader::CONTENT_TYPE` | `content-type` |
| `HttpHeader::LOCATION` | `location` |
| `HttpHeader::USER_AGENT` | `user-agent` |
| `HttpHeader::X_FORWARDED_FOR` | `x-forwarded-for` |

Refer to the source for the full list of constants.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpHeader;
use Raxos\Http\Response\JsonHttpResponse;

$response = new JsonHttpResponse(['status' => 'ok']);
$response->header(HttpHeader::CACHE_CONTROL, 'no-store', replace: true);
```

The constants also pair with the header map on a request:

```php
use Raxos\Http\HttpRequest;

$request = HttpRequest::createFromGlobals();
$agent = $request->headers->get(HttpHeader::USER_AGENT);
```
