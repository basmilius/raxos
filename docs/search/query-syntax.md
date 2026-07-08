---
outline: deep
---

# Query syntax

A free text query passed to `SearchProvider::search()` is turned into structured nodes in two steps. A `Lexer` splits the raw string into tokens, and a `Parser` groups those tokens into query nodes. Each node is one of the concrete classes in `Raxos\Search\Query\Token`, and a filter reads its value after narrowing the node with an `instanceof` check.

## The shape of a query

A query is a sequence of parts separated by whitespace. Each part is one of:

- A plain word, for example `release`.
- A quoted phrase, for example `"release notes"`.
- A field, written `key:value`, for example `status:published`.

Plain words and quoted phrases that are not attached to a field are collected together into the free text of the query. When the query is run, that combined free text is set on the `q` filter property, so a model that declares a `#[Filter('q', ...)]` receives it.

```text
release notes status:published published:2024-01-01..2024-12-31
```

In the example above `release notes` becomes the free text, `status:published` targets the `status` filter, and `published:2024-01-01..2024-12-31` targets the `published` filter with a date range.

## Field values

The value after a `key:` can be:

- A single word: `status:published`.
- A quoted phrase: `title:"release notes"`.
- A range written `from..to`, where either side may be omitted: `price:10..20`, `price:10..`, `price:..20`.

When a field value is a single bare word, the parser inspects it:

- A value that matches `2024-01-31` becomes a `DateValue`.
- A value that looks numeric (integer or float, optionally signed) becomes a `NumberValue`.
- Anything else stays a `Word`.

Inside a range, each endpoint follows the same rule: numeric endpoints become `NumberValue`, date endpoints become `DateValue`. Mixed or non-numeric range endpoints are rejected.

## The node classes

The parser produces the following node types, all under `Raxos\Search\Query\Token`:

| Node             | Represents                                             |
|------------------|--------------------------------------------------------|
| `Word`           | A single bare word (implements the text node contract).|
| `Phrase`         | A quoted phrase, and the collected free text.          |
| `Words`          | Several words grouped as one text value.               |
| `Field`          | A `key:value` pair, its value is another node or null. |
| `RangeValue`     | A `from..to` range, each side a value node or null.    |
| `NumberValue`    | An integer or float endpoint.                          |
| `DateValue`      | A date endpoint (`Y-m-d`).                             |
| `DateTimeValue`  | A datetime endpoint, used by structured input.         |
| `Query`          | The whole parsed query, a list of nodes.               |

`Word`, `Phrase` and `Words` implement the text node contract, so a filter that only needs text (like `Text` or `NaturalText`) can accept any of them.

## Reading a node in a filter

A filter receives one node as the `$searchQuery` parameter and narrows it before reading its value. This snippet accepts a numeric value or a numeric range and rejects everything else:

```php
use Raxos\Search\Error\InvalidFilterValueException;
use Raxos\Search\Query\Token as T;

if ($searchQuery instanceof T\RangeValue) {
    $from = $searchQuery->from instanceof T\NumberValue ? $searchQuery->from->value : null;
    $to = $searchQuery->to instanceof T\NumberValue ? $searchQuery->to->value : null;
    // ...
} elseif ($searchQuery instanceof T\NumberValue) {
    $value = $searchQuery->value;
    // ...
} else {
    throw new InvalidFilterValueException(self::class);
}
```

## Errors

Malformed input raises exceptions from `Raxos\Search\Error`:

- `UnexpectedTokenException` when the parser expects a token type it does not find.
- `InvalidRangeEndpointException` when a range endpoint is neither a number nor a date.

Both extend the base exception from [raxos/error](/error/), so they carry a stable error code and description.
