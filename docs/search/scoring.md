---
outline: deep
---

# Scoring

Search does not just filter rows, it ranks them. Every filter that runs contributes a weighted SQL expression, those expressions are summed into a `__score` column, and the combined results across every model are sorted by that score before the limit is applied.

## Per filter score expressions

A filter's `apply()` method returns a [`ScoreExpression`](/search/api/ScoreExpression): a SQL expression together with an integer weight. When the expression is compiled, it is multiplied by its weight, so a higher weight makes a filter count for more in the ranking.

Filters that only restrict rows return a zero expression, so they influence which rows match but not their order:

```php
use Raxos\Database\Query\Literal\Literal;
use Raxos\Search\ScoreExpression;

return new ScoreExpression(
    expression: Literal::of(0),
    weight: $this->weight
);
```

`Boolean`, `Exact`, `Defined`, `DateTime`, `Enum` and `Exists` all return a zero expression: they narrow the result set without changing the ranking.

## Filters that build a real score

Some filters build a scoring expression from the matched column:

- `Number` returns a `CASE` expression. An exact match scores `100`; a value inside a numeric range scores higher the closer it sits to the midpoint of the range.
- `NaturalText` uses the MySQL `MATCH ... AGAINST` relevance value itself as the score, and defaults to a weight of `3` so full text relevance dominates plain restriction filters.
- `Some` returns the greatest of its sub filter scores.

## Combining into `__score`

For each model, `SearchProvider` collects the `ScoreExpression` returned by every matched filter into a single `ScoreExpressions` value. That sum is selected as a `__score` column, and the query orders by it descending:

```sql
select ..., ( <expr1> * w1 + <expr2> * w2 + ... ) as __score
from ...
order by __score desc
limit ...
```

Each model is queried with its own `limit`, then the results from every registered model are gathered, wrapped in [`SearchResult`](/search/api/SearchResult) with their score read back from `__score`, re-sorted by score descending, and sliced to the requested `limit`.

## Reading the score back

A `SearchResult` exposes the numeric score alongside the model:

```php
foreach ($results as $result) {
    printf("%.2f  %s\n", $result->score, $result->model->title);
}
```

Because a search runs each model independently and then merges, a single query string can return a mixed, ranked list drawn from several model types at once.
