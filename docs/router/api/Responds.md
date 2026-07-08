---
outline: deep
---

# Responds

`Raxos\Router\Responds` is a trait for controllers and middleware. It adds short protected helper methods for building common `HttpResponse` instances. Use it with `use Responds;` inside your class.

```php
trait Responds
{
    // ...
}
```

## Methods

### json

```php
protected function json(mixed $body, HttpHeadersMap $headers = new HttpHeadersMap(), HttpResponseCode $responseCode = HttpResponseCode::OK): HttpResponse
```

Returns a JSON response.

### html

```php
protected function html(string $body, HttpHeadersMap $headers = new HttpHeadersMap(), HttpResponseCode $responseCode = HttpResponseCode::OK): HttpResponse
```

Returns an HTML response.

### result

```php
protected function result(mixed $result, HttpHeadersMap $headers = new HttpHeadersMap(), HttpResponseCode $responseCode = HttpResponseCode::OK): HttpResponse
```

Returns a generic result response, the same wrapper the router applies automatically to non-`HttpResponse` return values.

### redirect

```php
protected function redirect(string $destination, HttpHeadersMap $headers = new HttpHeadersMap(), HttpResponseCode $responseCode = HttpResponseCode::FOUND): HttpResponse
```

Returns a redirect response, defaulting to status `302 Found`.

### file

```php
protected function file(string $path, HttpRequest $request, HttpHeadersMap $headers = new HttpHeadersMap()): HttpResponse
```

Returns a file response for the given path, honoring conditional request headers.

### binary

```php
protected function binary(string $data, HttpHeadersMap $headers = new HttpHeadersMap()): HttpResponse
```

Returns a raw binary response.

### noContent

```php
protected function noContent(HttpHeadersMap $headers = new HttpHeadersMap()): HttpResponse
```

Returns a `204 No Content` response.

### notFound

```php
protected function notFound(): HttpResponse
```

Returns a `404 Not Found` response.

### forbidden

```php
protected function forbidden(HttpHeadersMap $headers = new HttpHeadersMap()): HttpResponse
```

Returns a `403 Forbidden` response.

### error

```php
protected function error(Throwable&JsonSerializable $err): HttpResponse
```

Returns a JSON error response for a JSON-serializable exception, using its `responseCode` property when present.

### validationError

```php
protected function validationError(string $field, string $constraint, string $message, array $params = []): HttpResponse
```

Returns a JSON validation error response for a single field and constraint.

### validationErrors

```php
protected function validationErrors(array ...$errors): HttpResponse
```

Returns a JSON validation error response for multiple fields and constraints at once.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Raxos\Router\Attribute\{Controller, Get};
use Raxos\Router\Responds;

#[Controller('/todos')]
final readonly class TodoController
{
    use Responds;

    #[Get('/')]
    public function index(): mixed
    {
        return $this->json(['todos' => []]);
    }
}
```

See [Building responses](/router/responses) for more.
