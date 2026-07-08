---
outline: deep
---

# Filter classes

The built in `FilterInterface` implementations live in `Raxos\Search\Filter`. Each one narrows a query and returns a [`ScoreExpression`](/search/api/ScoreExpression). Every filter shares three constructor tails, `?string $modelClass = null`, `?string $modelKey = null` and `int $weight`, which select the target model and column and set the ranking weight. When `modelClass` is null the model being searched is used, and when `modelKey` is null the filter's property name is used.

See the [filters concept](/search/filters) for how these are declared on a model and how to write your own.

## Boolean

`Raxos\Search\Filter\Boolean`

```php
public function __construct(
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

Matches a column against a `true`/`1` style boolean word: the value `true` or `1` matches a truthy column, anything else matches a falsy column. Implements `StructuredFilterInterface`. Returns a zero score expression.

## DateTime

`Raxos\Search\Filter\DateTime`

```php
public function __construct(
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

A range filter over a date or datetime column. In query-string mode it accepts a range value with date endpoints (`published:2024-01-01..2024-12-31`). In structured mode it reads a `property_after` and `property_before` pair from request parameters, parsing each with [raxos/datetime](/datetime/). Implements `StructuredFilterInterface`. Returns a zero score expression.

## Defined

`Raxos\Search\Filter\Defined`

```php
public function __construct(
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

A boolean filter over a column's null-ness: a truthy input matches rows where the column is not null, a falsy input matches rows where it is null. Useful for a "was X used?" filter over a nullable foreign key. Implements `StructuredFilterInterface`. Returns a zero score expression.

## Enum

`Raxos\Search\Filter\Enum`

```php
public function __construct(
    public string $enum,
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

Matches a column against one case of a backed enum. The input value is validated against the enum's cases and a value that matches no case throws `InvalidFilterValueException`. Implements `StructuredFilterInterface`, and `describe()` exposes the allowed values as an `enum`. Returns a zero score expression.

## Every

`Raxos\Search\Filter\Every`

Declared as an AND combinator for sub filters, but `apply()` currently throws `RuntimeException('Not implemented.')`. Use [`Some`](#some) for OR combinations instead.

## Exact

`Raxos\Search\Filter\Exact`

```php
public function __construct(
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

Matches a column against an exact word value. Implements `StructuredFilterInterface`. Returns a zero score expression.

## Exists

`Raxos\Search\Filter\Exists`

```php
public function __construct(
    public string $relation,
    public array $on,
    public ?string $matchKey = null,
    public ?string $enum = null,
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

A correlated `EXISTS` / `NOT EXISTS` filter against a related table. `$relation` is the related model class and `$on` is a map of relation column to model column used to correlate the subquery. Without a `$matchKey` a boolean input toggles between `EXISTS` and `NOT EXISTS`. With a `$matchKey` the input is matched against that column on the related table, optionally validated against `$enum`. Implements `StructuredFilterInterface`. Returns a zero score expression.

## NaturalText

`Raxos\Search\Filter\NaturalText`

```php
public function __construct(
    public array $keys = [],
    public bool $booleanMode = false,
    public bool $queryExpansion = false,
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 3
)
```

A MySQL `MATCH ... AGAINST` full text filter over one or more columns. `$keys` lists the columns to match; when empty it falls back to the filter's own column. `$booleanMode` and `$queryExpansion` switch the full text search mode. The relevance value doubles as the scoring expression, and the default weight of `3` lets full text relevance outweigh plain restriction filters.

## Number

`Raxos\Search\Filter\Number`

```php
public function __construct(
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

Matches a column against a single number or a numeric range. An exact match scores `100`; a value inside a `from..to` range scores higher the closer it sits to the midpoint. A half open range (only `from` or only `to`) applies the matching bound. A non-numeric node throws `InvalidFilterValueException`.

## Some

`Raxos\Search\Filter\Some`

```php
public function __construct(
    public array $filters,
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

Combines several filters with OR semantics inside one parenthesis, feeding the same query node to each. The score is the greatest of the sub filter scores. This is the supported way to combine filters. Implements `StructuredFilterInterface`.

## Text

`Raxos\Search\Filter\Text`

```php
public function __construct(
    public ?string $modelClass = null,
    public ?string $modelKey = null,
    public int $weight = 1
)
```

Matches a column with a substring `LIKE` search (`%value%`). Accepts any text node. Returns a zero score expression.

## Related

- [Filters](/search/filters): declaring filters and writing custom ones.
- [ScoreExpression](/search/api/ScoreExpression): what a filter returns to contribute to the ranking.
