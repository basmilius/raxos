---
outline: deep
---

# Attributes

The model attributes in `Raxos\Search\Attribute`. Each one is a `final readonly` class. The `#[Policy]` attribute is documented separately on the [Policy](/search/api/Policy) page.

## #[Filter]

`Raxos\Search\Attribute\Filter`

A repeatable class attribute that maps an input property name to a filter instance.

```php
public function __construct(
    public string $property,
    public FilterInterface $filter
)
```

- `$property` is the name a `key:value` query part or an HTTP parameter targets. The free text of a query is assigned to the `q` property.
- `$filter` is the `FilterInterface` instance that narrows the query and returns a score.

```php
use Raxos\Search\Attribute\Filter;
use Raxos\Search\Filter\{Exact, NaturalText};

#[Filter('author_id', filter: new Exact(modelKey: 'author_id'))]
#[Filter('q', filter: new NaturalText(keys: ['title', 'body']))]
final class Article extends Model { /* ... */ }
```

See the [filters concept](/search/filters) and the [filter class reference](/search/api/Filters).

## #[Preset]

`Raxos\Search\Attribute\Preset`

A repeatable class attribute describing a named set of default filter values for a model.

```php
public function __construct(
    public string $name,
    public array $filters
)
```

- `$name` identifies the preset.
- `$filters` is a map of filter property to default value.

The preset is stored on the generated search model for the application to read. The package itself does not apply presets automatically; you decide when and how to seed a search with a preset's values.

```php
use Raxos\Search\Attribute\Preset;

#[Preset('recent', ['published' => '2024-01-01..'])]
final class Article extends Model { /* ... */ }
```

To read the presets declared on a model, generate its search model directly with [`SearchModelGenerator`](/search/api/SearchModel):

```php
use App\Model\Article;
use Raxos\Search\SearchModelGenerator;

$presets = SearchModelGenerator::generate(Article::class)->presets;
```

## #[SelectOption]

`Raxos\Search\Attribute\SelectOption`

A class attribute describing how a model is presented as type-ahead select options: which columns are searched, the ordering and the result limits.

```php
public function __construct(
    public array $searchKeys,
    public ?string $order = null,
    public bool $descending = false,
    public int $limit = 25,
    public ?int $emptyLimit = null
)
```

- `$searchKeys` are the columns matched with a substring `LIKE` search.
- `$order` is the order-by column; it defaults to the first search key.
- `$descending` orders descending when true.
- `$limit` caps the results when a search term is present.
- `$emptyLimit` caps the results with no search term; it defaults to `$limit`.

Like presets, this attribute carries configuration only. Reading it with reflection and building the query is left to the application. See the [select options concept](/search/select-options).

```php
use Raxos\Search\Attribute\SelectOption;

#[SelectOption(searchKeys: ['name', 'email'], order: 'name', limit: 25, emptyLimit: 10)]
final class User extends Model { /* ... */ }
```

## Related

- [Policy](/search/api/Policy): the `#[Policy]` attribute and its decision types.
- [Filter classes](/search/api/Filters): the filter instances `#[Filter]` points to.
- [SearchModel](/search/api/SearchModel): the generated model these attributes end up on.
