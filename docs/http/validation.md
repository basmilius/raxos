---
outline: deep
---

# Request validation

The validator converts a raw array of data into a typed, validated object. It is attribute driven: you describe the shape of a request as a class, mark its properties, and let `HttpValidator` build and check the instance in one call.

## Request models

A request model is a class that implements `Raxos\Contract\Http\HttpRequestModelInterface`. Each constructor promoted property that should be filled from the input is marked with the `#[Property]` attribute:

```php
<?php
declare(strict_types=1);

namespace App\Http\Request;

use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Http\Validate\Attribute\Property;
use Raxos\Http\Validate\Constraint\{Email, MaxLength, MinLength};

final readonly class CreateAccountRequestModel implements HttpRequestModelInterface
{
    public function __construct(
        #[Property]
        #[MinLength(2)]
        #[MaxLength(64)]
        public string $name,

        #[Property]
        #[Email]
        public string $email
    ) {}
}
```

The `#[Property]` attribute takes two optional arguments:

- `alias`: read the value from a different key than the property name.
- `optional`: either a boolean, or a closure `fn(string $name, Property $property): bool` that decides at runtime whether the property may be missing.

```php
#[Property(alias: 'email_address', optional: true)]
public ?string $email = null;
```

## Running the validator

`HttpValidator::validate()` builds and validates the model in a single call and returns the populated instance. Internally it delegates to `HttpClassValidator`:

```php
<?php
declare(strict_types=1);

use App\Http\Request\CreateAccountRequestModel;
use Raxos\Http\Validate\HttpValidator;

$model = HttpValidator::validate(CreateAccountRequestModel::class, [
    'name' => 'Bas',
    'email' => 'bas@mili.us'
]);

$model->name;   // 'Bas'
$model->email;  // 'bas@mili.us'
```

## Type coercion

Before the constraints run, the validator coerces values to match the property type:

- Properties typed `bool`, `float` or `int` are passed through a built in transformer that converts the raw value.
- Properties typed as a `BackedEnum` are resolved with `tryFrom()`; an unknown value fails validation.
- Properties typed as another `HttpRequestModelInterface` are validated recursively as a nested model.

## Constraint attributes

Constraint attributes live in `Raxos\Http\Validate\Constraint` and add extra checks or transformations on top of a `#[Property]`. They run in the order they are declared.

| Attribute | Purpose |
| --- | --- |
| `Choice(array $options)` | The value must be one of the given options. |
| `Email` | The value must be a valid email address. |
| `Url` | The value must be a valid URL. |
| `Matches(string $pattern)` | The value must match the given regular expression. |
| `Min(int\|float $min)` | A numeric value must be at least `min`. |
| `Max(int\|float $max)` | A numeric value must be at most `max`. |
| `MinLength(int $min)` | A string must be at least `min` characters long. |
| `MaxLength(int $max)` | A string must be at most `max` characters long. |
| `Date` | Parses the value into a raxos/datetime `Date`. |
| `DateTime` | Parses the value into a raxos/datetime `DateTime`. |
| `Time` | Parses the value into a raxos/datetime `Time`. |
| `Upload` | Resolves the value to an uploaded `HttpFile`. |
| `Nested` | Validates an array value as a nested request model. |
| `NestedArray(string $propertyType)` | Validates a list of arrays as request models. |
| `Model` | Resolves the value into a raxos/database model. |
| `ModelArray(string $modelClass)` | Resolves a list of values into raxos/database models. |

The `Date`, `DateTime` and `Time` constraints return value objects from [datetime](/datetime/). The `Model` and `ModelArray` constraints resolve values into [database](/database/) models and therefore require that package.

```php
<?php
declare(strict_types=1);

namespace App\Http\Request;

use App\Model\Country;
use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\DateTime\Date;
use Raxos\Http\Validate\Attribute\Property;
use Raxos\Http\Validate\Constraint\{Choice, Date as DateConstraint, Model};

final readonly class ProfileRequestModel implements HttpRequestModelInterface
{
    public function __construct(
        #[Property]
        #[Choice(['nl', 'en', 'de'])]
        public string $language,

        #[Property]
        #[DateConstraint]
        public Date $birthday,

        #[Property]
        #[Model]
        public Country $country
    ) {}
}
```

## Handling failures

When one or more properties are invalid, `HttpValidator::validate()` throws a `ValidationNotOkException`. It carries an `errors` array with one constraint exception per invalid property, keyed by the property name (or its alias). Each constraint has its own dedicated exception class in `Raxos\Http\Validate\Error`, for example `EmailConstraintException` or `MinLengthConstraintException`.

```php
<?php
declare(strict_types=1);

use App\Http\Request\CreateAccountRequestModel;
use Raxos\Http\Validate\Error\ValidationNotOkException;
use Raxos\Http\Validate\HttpValidator;

try {
    $model = HttpValidator::validate(CreateAccountRequestModel::class, $data);
} catch (ValidationNotOkException $err) {
    foreach ($err->errors as $property => $exception) {
        // report each failed property
    }
}
```

Because `ValidationNotOkException` extends the base `Raxos\Error\Exception`, it is JSON serializable out of the box, which makes it straightforward to return as an error response.

## Related

- [router](/router/) wires this validator into controller actions through its `#[Validated]` attribute.
