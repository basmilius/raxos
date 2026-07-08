---
outline: deep
---

# SearchModel

`Raxos\Search\SearchModel` and `Raxos\Search\SearchModelGenerator` are the pieces behind `SearchProvider::registerModel()`. A `SearchModel` is a read only snapshot of a model's search configuration (its ORM structure, filters, policies and presets), built once by reading the model's attributes with reflection. `SearchProvider` builds one internally for every registered model, and you can generate one directly whenever you need to inspect a model's search configuration outside of a search or a filtered query, for example to read its presets or to list a filter's OpenAPI shape.

## SearchModel

`Raxos\Search\SearchModel`

A final readonly class holding the generated search configuration for one model. It implements `DebuggableInterface`.

### Signature

```php
/**
 * @template TModel of Model
 */
final readonly class SearchModel
{
    public function __construct(
        public StructureInterface $structure,
        public array $filters = [],
        public array $policies = [],
        public array $presets = []
    ) {}
}
```

- `$structure` is the ORM structure of the model from [raxos/database](/database/).
- `$filters` maps a filter property name to its `#[Filter]` attribute instance.
- `$policies` lists the `PolicyInterface` instances from every `#[Policy]` attribute, in declaration order.
- `$presets` lists the `#[Preset]` attributes declared on the model.

## SearchModelGenerator

`Raxos\Search\SearchModelGenerator`

A final class with a single static method that builds a `SearchModel` from a model class by reading its `#[Filter]`, `#[Policy]` and `#[Preset]` attributes.

### generate

```php
public static function generate(string $modelClass): SearchModel
```

Reflects over `$modelClass`, sorts its attributes into filters, policies and presets, and returns a `SearchModel`. `SearchProvider::registerModel()` calls this once per model class and caches the result. Calling it yourself is safe and cheap, and is the only way to read a model's presets or to list its filters for documentation purposes, since `SearchProvider` does not expose its internal cache of registered models.

Wraps any `ReflectionException` raised while reflecting over the model in a [`ReflectionErrorException`](#reflectionerrorexception).

### Usage

Reading the presets declared on a model:

```php
<?php
declare(strict_types=1);

use App\Model\Article;
use Raxos\Search\SearchModelGenerator;

$model = SearchModelGenerator::generate(Article::class);

foreach ($model->presets as $preset) {
    echo "{$preset->name}: " . json_encode($preset->filters) . PHP_EOL;
}
```

Listing the OpenAPI shape of every structured filter on a model:

```php
use Raxos\Contract\Search\StructuredFilterInterface;
use Raxos\Search\SearchModelGenerator;

$model = SearchModelGenerator::generate(Article::class);

foreach ($model->filters as $property => $attribute) {
    if ($attribute->filter instanceof StructuredFilterInterface) {
        print_r($attribute->filter->describe($property));
    }
}
```

## ReflectionErrorException

`Raxos\Search\Error\ReflectionErrorException`

Thrown by `SearchModelGenerator::generate()` when reflecting over the model class fails. It carries the originating `ReflectionException` in its `$err` property, and extends the base exception from [raxos/error](/error/).

## Related

- [SearchProvider](/search/api/SearchProvider): calls `SearchModelGenerator::generate()` internally and caches the result per model class.
- [Attributes](/search/api/Attributes): the `#[Filter]`, `#[Policy]` and `#[Preset]` attributes collected into a `SearchModel`.
