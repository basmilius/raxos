---
outline: deep
---

# HttpMethod

`Raxos\Http\HttpMethod`

A backed string enum of the HTTP verbs used throughout the router and client.

```php
enum HttpMethod: string
```

## Cases

| Case | Value |
| --- | --- |
| `ANY` | `ANY` |
| `DELETE` | `DELETE` |
| `GET` | `GET` |
| `HEAD` | `HEAD` |
| `OPTIONS` | `OPTIONS` |
| `PATCH` | `PATCH` |
| `POST` | `POST` |
| `PUT` | `PUT` |

The `ANY` case is a wildcard used by the router to match every verb; it is not a real HTTP method.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpMethod;

$method = HttpMethod::from('GET');

if ($method === HttpMethod::GET) {
    // handle a read request
}
```

Because `HttpRequest::createFromGlobals()` resolves the method for you, the enum usually arrives ready to compare:

```php
use Raxos\Http\HttpRequest;

$request = HttpRequest::createFromGlobals();

if ($request->method === HttpMethod::POST) {
    $payload = $request->json();
}
```
