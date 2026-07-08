---
outline: deep
---

# HttpResponseCode

`Raxos\Http\HttpResponseCode`

A backed integer enum covering every standard HTTP status code from 100 to 511, across the informational, success, redirection, client error and server error ranges.

```php
enum HttpResponseCode: int
```

## Methods

```php
public function getMessage(): string
```

Returns the standard reason phrase for the status code, for example `Not Found` for `HttpResponseCode::NOT_FOUND`.

## Example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpResponseCode;

$code = HttpResponseCode::NOT_FOUND;

$code->value;         // 404
$code->getMessage();  // 'Not Found'
```

Build an enum from an integer status with `from()` or `tryFrom()`:

```php
$code = HttpResponseCode::from(201);   // HttpResponseCode::CREATED
```

Responses take a status code through their constructor or the `responseCode()` method:

```php
use Raxos\Http\Response\JsonHttpResponse;

$response = new JsonHttpResponse(['error' => 'gone']);
$response->responseCode(HttpResponseCode::GONE);
```
