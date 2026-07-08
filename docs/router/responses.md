---
outline: deep
---

# Building responses

A route handler decides what to return. The router accepts both plain values and `HttpResponse` instances, and the `Responds` trait provides short helpers for the common cases.

## Returning plain values

A route method or closure may return any value. When the returned value is not already an `HttpResponse`, the router wraps it in a `ResultHttpResponse` automatically. This is the shortest way to answer a request with JSON-serializable data.

```php
use Raxos\Router\Attribute\{Controller, Get};

#[Controller('/todos')]
final readonly class TodoController
{
    #[Get('/')]
    public function index(): array
    {
        return ['todos' => []];
    }
}
```

## Returning a response directly

When you need control over the status code, headers or body type, return an `HttpResponse` instance. The router uses it as is. The `Responds` trait builds these for you.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controller;

use Raxos\Http\HttpResponseCode;
use Raxos\Router\Attribute\{Controller, Get, Post};
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

    #[Post('/')]
    public function store(): mixed
    {
        return $this->json(['created' => true], responseCode: HttpResponseCode::CREATED);
    }
}
```

## Available helpers

The `Responds` trait, used with `use Responds;`, adds a helper per common response type. Each returns an `HttpResponse`:

- `json`: a JSON response.
- `html`: an HTML response.
- `result`: a generic result response, the same wrapper the router applies to plain return values.
- `redirect`: a redirect response, defaulting to status `302 Found`.
- `file`: a file response for a path, honoring conditional request headers.
- `binary`: a raw binary response.
- `noContent`: a `204 No Content` response.
- `notFound`: a `404 Not Found` response.
- `forbidden`: a `403 Forbidden` response.
- `error`: a JSON error response for a JSON-serializable exception, using its `responseCode` when present.

```php
use Raxos\Http\HttpRequest;

#[Get('/$id/download')]
public function download(HttpRequest $request, int $id): mixed
{
    return $this->file("/var/todos/{$id}.pdf", $request);
}

#[Get('/redirect')]
public function toIndex(): mixed
{
    return $this->redirect('/todos');
}
```

## Validation errors

Two helpers build a standardized JSON validation error response. Use `validationError` for a single field and constraint, and `validationErrors` for several at once.

```php
#[Post('/')]
public function store(HttpRequest $request): mixed
{
    if ($request->post->get('title') === null) {
        return $this->validationError('title', 'required', 'A title is required.');
    }

    return $this->json(['created' => true]);
}
```
