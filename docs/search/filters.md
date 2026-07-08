---
outline: deep
---

# Filters

A filter maps an input property to a piece of query logic. You attach filters to a model with the repeatable `#[Filter]` attribute, and each one points to a filter instance that knows how to narrow a query and how much it contributes to the ranking score.

## Declaring filters on a model

`#[Filter]` takes the input property name and a filter instance. It is repeatable, so a model can declare as many filters as it needs.

```php
<?php
declare(strict_types=1);

namespace App\Model;

use Raxos\Database\Orm\Attribute\{Column, PrimaryKey, Table};
use Raxos\Database\Orm\Model;
use Raxos\Search\Attribute\Filter;
use Raxos\Search\Filter\{DateTime, Exact, NaturalText};

#[Table('articles')]
#[Filter('author_id', filter: new Exact(modelKey: 'author_id'))]
#[Filter('published', filter: new DateTime(modelKey: 'published_at'))]
#[Filter('q', filter: new NaturalText(keys: ['title', 'body']))]
final class Article extends Model
{
    #[PrimaryKey]
    #[Column]
    public int $id;

    #[Column]
    public string $title;

    #[Column]
    public string $body;
}
```

The property name is what a `key:value` query part or an HTTP parameter targets. The free text of a query is always assigned to the `q` property, so a `#[Filter('q', ...)]` acts as the model's main text search.

## The filter contracts

Every filter implements `FilterInterface`, which exposes three read-only properties (`modelClass`, `modelKey`, `weight`) and one method:

```php
public function apply(
    StructureInterface $structure,
    Filter $attribute,
    QueryInterface $query,
    QueryNodeInterface $searchQuery
): ScoreExpression;
```

`apply()` adds conditions to the query and returns a [`ScoreExpression`](/search/api/ScoreExpression) describing this filter's contribution to the ranking. A filter resolves the target column from `modelClass` (defaulting to the model being searched) and `modelKey` (defaulting to the filter's property name).

A filter that also implements `StructuredFilterInterface` can be driven from plain request parameters and documented for OpenAPI:

```php
public function fromInput(string $property, MapInterface $params): ?QueryNodeInterface;
public function describe(string $property): array;
```

`fromInput()` reads the value from a map of request parameters and returns a query node (or null when the filter has no value), which is then fed to the same `apply()`. `describe()` returns a framework-neutral shape (a name, a type, optionally an `enum` or `format`) used for documentation.

## Built in filters

The package ships the following filter classes in `Raxos\Search\Filter`. See the [filter class reference](/search/api/Filters) for constructor signatures.

| Filter        | Behaviour                                                                    | Structured |
|---------------|------------------------------------------------------------------------------|------------|
| `Boolean`     | Matches a column against a `true`/`1` style boolean word.                     | yes        |
| `DateTime`    | Range over a date or datetime column, read from `_after` and `_before` keys.  | yes        |
| `Defined`     | Matches on a column being null or not null.                                   | yes        |
| `Enum`        | Matches a column against one case of a backed enum, validating the value.     | yes        |
| `Every`       | Declared as an AND combinator, but `apply()` currently throws. Use `Some`.    | no         |
| `Exact`       | Matches a column against an exact word value.                                 | yes        |
| `Exists`      | Correlated `EXISTS` / `NOT EXISTS` against a related table.                    | yes        |
| `NaturalText` | MySQL `MATCH ... AGAINST` full text match over one or more columns.           | no         |
| `Number`      | Matches a single number or a numeric range, with a closeness score.           | no         |
| `Some`        | Combines several filters with OR semantics inside one parenthesis.            | yes        |
| `Text`        | Substring `LIKE` match on a column.                                           | no         |

::: warning Every is not implemented
`Every` is declared as an AND combinator, but its `apply()` throws a `RuntimeException('Not implemented.')`. To combine several filters, use `Some`, which is the supported OR combinator.
:::

## Combining filters with Some

`Some` runs several sub filters inside one parenthesis and joins their conditions with OR. Its score is the greatest of the sub scores.

```php
use Raxos\Search\Attribute\Filter;
use Raxos\Search\Filter\{Some, Text};

#[Filter('q', filter: new Some([
    new Text(modelKey: 'title'),
    new Text(modelKey: 'body'),
]))]
```

A query part `q:draft` then matches rows whose `title` or `body` contains `draft`.

## Custom filters

A custom filter narrows the node it receives, adds its own conditions and returns a score expression. Implementing `StructuredFilterInterface` in addition lets the same class serve request parameters.

```php
<?php
declare(strict_types=1);

namespace App\Search;

use Raxos\Contract\Collection\MapInterface;
use Raxos\Contract\Database\Orm\StructureInterface;
use Raxos\Contract\Database\Query\QueryInterface;
use Raxos\Contract\Search\{FilterInterface, QueryNodeInterface, StructuredFilterInterface};
use Raxos\Database\Query\Literal\Literal;
use Raxos\Search\Attribute\Filter;
use Raxos\Search\Error\InvalidFilterValueException;
use Raxos\Search\Query\Token as T;
use Raxos\Search\ScoreExpression;

final readonly class ActiveFilter implements FilterInterface, StructuredFilterInterface
{
    public function __construct(
        public ?string $modelClass = null,
        public ?string $modelKey = null,
        public int $weight = 1
    ) {}

    public function apply(StructureInterface $structure, Filter $attribute, QueryInterface $query, QueryNodeInterface $searchQuery): ScoreExpression
    {
        if (!($searchQuery instanceof T\Word)) {
            throw new InvalidFilterValueException(self::class);
        }

        $modelClass = $this->modelClass ?? $structure->class;
        $modelKey = $this->modelKey ?? $attribute->property;

        $query->where($modelClass::col($modelKey), $searchQuery->text === 'true');

        return new ScoreExpression(Literal::of(0), weight: $this->weight);
    }

    public function fromInput(string $property, MapInterface $params): ?QueryNodeInterface
    {
        if (!$params->has($property)) {
            return null;
        }

        return new T\Word((string)$params->get($property));
    }

    public function describe(string $property): array
    {
        return [['name' => $property, 'type' => 'boolean']];
    }
}
```

When the node type a filter receives does not match what it expects, it throws `InvalidFilterValueException`, which extends the base exception from [raxos/error](/error/).

## Where to next

- [Scoring](/search/scoring): how the score expression a filter returns is combined into the final ranking.
- [Policies](/search/policies): guarding a model before its filters run.
