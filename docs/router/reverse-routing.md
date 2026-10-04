# Building route URLs

`url()` uses the registered plain template for a controller handler. `path()` retains its existing compiled-path behavior.

```php
$url = $router->url(
    [ArticleController::class, 'show'],
    parameters: ['id' => 42],
    query: ['language' => 'nl']
);
```

A handler with `#[Get('/articles/$id')]` produces `/articles/42?language=nl`. Strings, numbers, booleans, backed enums and StringParsable values use encoded path segments. Unicode, spaces, slashes and percent signs are encoded once, and dispatch decodes captured segments once. Query parameters use RFC 3986 encoding.

Required path parameters must be supplied. Optional parameters with defaults can be omitted. Unknown parameters and values that fail the route's type pattern raise `InvalidRouteParametersException` instead of generating an unusable URL.

When a handler has multiple routes, the first registered matching route wins. Pass `method: HttpMethod::GET` or `template: '/articles/$id'` to choose explicitly. Templates and handlers must belong to registered routes; this API does not generate URLs for arbitrary controller methods.
