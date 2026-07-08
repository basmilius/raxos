---
outline: deep
---

# SearchResult

`Raxos\Search\SearchResult`

The value object returned by `SearchProvider::search()`. It pairs a computed score with the matched model. It is a final readonly class that implements `JsonSerializable`.

## Signature

```php
public function __construct(
    public float $score,
    public Model $model
)
```

- `$score` is the numeric ranking score read back from the `__score` column.
- `$model` is the matched ORM model instance from [raxos/database](/database/).

## Methods

### jsonSerialize

```php
public function jsonSerialize(): array
```

Returns `['score' => $this->score, 'model' => $this->model]`, so a list of results serializes directly to JSON with both the score and the model payload.

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\Search\{SearchProvider, SearchResult};

$results = $provider->search('release notes');

foreach ($results as $result) {
    /** @var SearchResult $result */
    printf("%.2f  %s\n", $result->score, $result->model->title);
}

// Serializes to [{"score": 12.5, "model": {...}}, ...]
echo json_encode($results);
```

## Related

- [SearchProvider](/search/api/SearchProvider): produces the list of results.
- [Scoring](/search/scoring): how the score is computed.
