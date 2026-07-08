---
outline: deep
---

# SearchProvider

`Raxos\Search\SearchProvider`

The main entry point. It registers models, runs a free text search across every registered model, and applies structured filters to a single query. It is a final class that implements `SearchProviderInterface`.

## Methods

### registerModel

```php
public function registerModel(string $modelClass): void
```

Generates and caches a search model for the given model class, reading its `#[Filter]`, `#[Policy]` and `#[Preset]` attributes. Calling it more than once for the same class is safe: the generated model is cached and reused. See [SearchModel](/search/api/SearchModel) for the shape of the generated model and for generating one directly, for example to read a model's presets.

### applyFilters

```php
public function applyFilters(
    QueryInterface $query,
    string $modelClass,
    MapInterface $params
): QueryInterface
```

Applies every `StructuredFilterInterface` filter declared on the model whose `fromInput()` finds a value in `$params`, extending the given query. This is the structured mode: the same `#[Filter]` declarations that power a search box also power a filter form, reading their values straight from request parameters. The model is registered automatically if needed.

### search

```php
public function search(
    string $query,
    ?MapInterface $context = null,
    ?MapInterface $filters = null,
    int $limit = 10
): ArrayListInterface
```

Tokenizes the free text query, runs it against every registered model (subject to each model's policies), scores and sorts the combined results, and returns the top items as an array list of [`SearchResult`](/search/api/SearchResult).

- `$context` is passed to every policy, typically to scope a query to the current tenant or user.
- `$filters` seeds the parsed filter values; the query's own field and free text nodes are merged on top of it.
- `$limit` caps both the per model queries and the final merged, re-sorted list.

## Usage

```php
<?php
declare(strict_types=1);

use App\Model\{Article, Product};
use Raxos\Collection\Map;
use Raxos\Search\{SearchProvider, SearchResult};

$provider = new SearchProvider();
$provider->registerModel(Article::class);
$provider->registerModel(Product::class);

$results = $provider->search(
    query: 'wireless price:20..80',
    context: new Map(['tenant_id' => 42]),
    limit: 20
);

foreach ($results as $result) {
    /** @var SearchResult $result */
    echo "{$result->score}: " . $result->model::class . PHP_EOL;
}
```

### Structured filters from request parameters

```php
<?php
declare(strict_types=1);

use App\Model\Article;
use Raxos\Search\SearchProvider;

$provider = new SearchProvider();

$query = $provider->applyFilters(
    query: Article::select(),
    modelClass: Article::class,
    params: $request->query
);

$articles = $query->array();
```

Any filter that implements `StructuredFilterInterface` reads its value straight from a map of request parameters, so one filter definition serves both a search box and a filter form. See [filters](/search/filters) for the list of structured filters.

## Related

- [SearchResult](/search/api/SearchResult): the value object returned by `search()`.
- [SearchModel](/search/api/SearchModel): the generated model `registerModel()` builds and caches.
- [Filters](/search/filters): the filter contracts and built in filter classes.
- [Policies](/search/policies): how a model guards or scopes itself before filters run.
