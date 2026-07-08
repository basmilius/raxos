---
outline: deep
---

# HttpValidator

`Raxos\Http\Validate\HttpValidator`

The static entry point for validating an array of data against a request model class. It is a thin wrapper that builds an `HttpClassValidator`, runs it and returns the populated instance.

```php
final class HttpValidator
```

## Methods

```php
public static function validate(string $class, array $data): object
```

Validates `$data` against the given request model class and returns a populated, validated instance. The class must implement `Raxos\Contract\Http\HttpRequestModelInterface`. On failure it throws a `ValidationNotOkException` that implements `Raxos\Contract\Http\Validate\ValidatorExceptionInterface`.

## Example

```php
<?php
declare(strict_types=1);

namespace App\Http\Request;

use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Http\Validate\Attribute\Property;
use Raxos\Http\Validate\Constraint\{Email, MinLength};

final readonly class CreateAccountRequestModel implements HttpRequestModelInterface
{
    public function __construct(
        #[Property]
        #[MinLength(2)]
        public string $name,

        #[Property]
        #[Email]
        public string $email
    ) {}
}
```

```php
<?php
declare(strict_types=1);

use App\Http\Request\CreateAccountRequestModel;
use Raxos\Http\Validate\Error\ValidationNotOkException;
use Raxos\Http\Validate\HttpValidator;

try {
    $model = HttpValidator::validate(CreateAccountRequestModel::class, [
        'name' => 'Bas',
        'email' => 'bas@mili.us'
    ]);
} catch (ValidationNotOkException $err) {
    foreach ($err->errors as $property => $exception) {
        // one constraint exception per invalid property
    }
}
```

See [request validation](/http/validation) for the full set of constraint attributes and the request model shape.
