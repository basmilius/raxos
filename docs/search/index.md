---
outline: deep

cards:
    highlights:
        -   title: SearchProvider
            code: true
            details: 'Register models, then run a free text search or a structured filter query.'
            link: /search/api/SearchProvider
        -   title: '#[Filter]'
            code: true
            details: 'Map an input property to a filter instance directly on the model.'
            link: /search/api/Attributes
        -   title: Filter classes
            code: true
            details: 'Ready to use filters for booleans, dates, enums, numbers and full text.'
            link: /search/api/Filters
        -   title: ScoreExpression
            code: true
            details: 'Contribute a weighted SQL expression to the combined ranking score.'
            link: /search/api/ScoreExpression
        -   title: '#[Policy]'
            code: true
            details: 'Scope, allow, deny or silently drop a search before any filter runs.'
            link: /search/api/Policy
        -   title: SearchResult
            code: true
            details: 'A score paired with the matched model, ready to serialize as JSON.'
            link: /search/api/SearchResult
---

# Search

Raxos Search adds a small query language, a scoring model and a filter and policy pipeline on top of [raxos/database](/database/). A model declares its searchable properties with the `#[Filter]` attribute, optionally guards access with `#[Policy]`, and can expose a compact type-ahead list with `#[SelectOption]`. `SearchProvider` tokenizes a free text query (field:value pairs, quoted phrases, numeric and date ranges), applies the matching filters per registered model, and ranks the combined results by a per filter weighted score.

The same filter definitions can also be driven directly from HTTP query parameters through `applyFilters()`, so one declaration serves both a free text search box and a structured filter form.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Query syntax](/search/query-syntax): the mini query language that the lexer and parser turn into structured nodes.
- [Filters](/search/filters): the `#[Filter]` attribute, the filter contracts and a tour of the built in filter classes.
- [Scoring](/search/scoring): how per filter score expressions combine into the `__score` column that ranks results.
- [Policies](/search/policies): the `#[Policy]` attribute, `PolicyDecision` and how a denied policy surfaces.
- [Select options](/search/select-options): the `#[SelectOption]` attribute for type-ahead dropdown sources.

## Quick example

```php
<?php
declare(strict_types=1);

use App\Model\Article;
use Raxos\Collection\Map;
use Raxos\Search\{SearchProvider, SearchResult};

$provider = new SearchProvider();
$provider->registerModel(Article::class);

$context = new Map(['tenant_id' => 42]);
$results = $provider->search(
    query: 'release notes published:2024-01-01..2024-12-31',
    context: $context,
    limit: 20
);

foreach ($results as $result) {
    /** @var SearchResult $result */
    echo "{$result->score}: {$result->model->title}" . PHP_EOL;
}
```

## Next steps

Head to the [installation](/search/installation) page to add the package to your project, then read the [query syntax](/search/query-syntax) and [filters](/search/filters) concepts to make a model searchable.
