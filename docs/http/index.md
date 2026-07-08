---
outline: deep

cards:
    highlights:
        -   title: HttpRequest
            code: true
            details: 'An immutable, cached wrapper around the incoming request built from the PHP superglobals.'
            link: /http/api/HttpRequest
        -   title: HttpResponse
            code: true
            details: 'A typed response hierarchy for JSON, HTML, redirects, files and binary output.'
            link: /http/api/HttpResponse
        -   title: HttpValidator
            code: true
            details: 'Turn raw arrays into validated, typed request model objects with attributes.'
            link: /http/api/HttpValidator
        -   title: HttpClient
            code: true
            details: 'A fluent outgoing HTTP client built on Guzzle with a PSR-18 bridge.'
            link: /http/api/HttpClient
        -   title: HttpMethod
            code: true
            details: 'The HTTP verbs used throughout the router and client as a backed enum.'
            link: /http/api/HttpMethod
        -   title: HttpResponseCode
            code: true
            details: 'Every standard status code from 100 to 511, with reason phrases.'
            link: /http/api/HttpResponseCode
---

# HTTP

Raxos HTTP provides the low level building blocks that the router and other packages build on. It wraps the incoming request in an immutable `HttpRequest` value object, exposes a family of typed `HttpResponse` classes for JSON, HTML, redirects, files and binary output, ships an outgoing `HttpClient` built on Guzzle with a PSR-18 and PSR-17 bridge, and includes an attribute driven validator that converts raw arrays into typed, validated request model objects.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Requests and responses](/http/requests-and-responses): build a request from the superglobals and return typed responses.
- [Headers and status codes](/http/headers-and-status-codes): the `HttpHeader` constants, the `HttpResponseCode` enum and the structure maps.
- [Request validation](/http/validation): attribute based request models, the `#[Property]` attribute and constraint attributes.
- [HTTP client](/http/http-client): the fluent outgoing client, its response wrapper and the PSR bridge.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpRequest;
use Raxos\Http\Response\JsonHttpResponse;
use Raxos\Http\HttpResponseCode;

$request = HttpRequest::createFromGlobals();

$response = new JsonHttpResponse(
    body: ['language' => $request->language()],
    responseCode: HttpResponseCode::OK
);

$response->send();
```

## Installation

Install the package with Composer. See [installation](/http/installation) for the required PHP version and extensions.

```shell
composer require raxos/http
```
