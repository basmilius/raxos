---
outline: deep
---

# ScoreExpression

The two classes a filter uses to contribute to the ranking score. Both live in `Raxos\Search` and implement `QueryExpressionInterface` from [raxos/database](/database/).

## ScoreExpression

`Raxos\Search\ScoreExpression`

Wraps one scoring expression and its weight. This is what a filter's `apply()` method returns. It is a final readonly class.

### Signature

```php
public function __construct(
    public QueryLiteralInterface|QueryExpressionInterface $expression,
    public array $params = [],
    public int $weight = 1
)
```

- `$expression` is the SQL expression to score with, either a literal or another query expression.
- `$params` holds any bound parameters for the expression.
- `$weight` multiplies the expression when it is compiled, so a higher weight makes the filter count for more.

### Methods

```php
public function compile(
    QueryInterface $query,
    ConnectionInterface $connection,
    GrammarInterface $grammar
): void
```

Compiles the wrapped expression followed by `* {weight}`.

### Usage

A restriction-only filter returns a zero expression so it does not affect ranking:

```php
use Raxos\Database\Query\Literal\Literal;
use Raxos\Search\ScoreExpression;

return new ScoreExpression(
    expression: Literal::of(0),
    weight: $this->weight
);
```

A ranking filter returns a real expression, for example a closeness score:

```php
use Raxos\Search\ScoreExpression;
use function Raxos\Database\Query\literal;

return new ScoreExpression(literal(
    "case when {$col} = {$value} then 100 else 0 end"
));
```

## ScoreExpressions

`Raxos\Search\ScoreExpressions`

Sums a list of `ScoreExpression` values into the `__score` column that `SearchProvider` selects and orders by. It is a final readonly class.

### Signature

```php
public function __construct(
    public array $expressions
)
```

`$expressions` is the list of `ScoreExpression` values collected from every filter that matched for a model.

### Methods

```php
public function compile(
    QueryInterface $query,
    ConnectionInterface $connection,
    GrammarInterface $grammar
): void
```

Compiles the expressions joined with `+`, wrapped in parentheses. `SearchProvider` builds this internally; you rarely construct it yourself.

## Related

- [Scoring](/search/scoring): how these expressions combine into the final ranking.
- [Filter classes](/search/api/Filters): which built in filters return a real score and which return zero.
