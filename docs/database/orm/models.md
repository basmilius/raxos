---
outline: deep
---

# Models

A model is a class that extends `Raxos\Database\Orm\Model` and describes a table through PHP attributes. Each mapped property becomes a column, and the ORM handles reading, hydrating, saving and deleting for you.

## Defining a model

Put `#[Table]` on the class and `#[Column]` on every mapped property. Mark the key with `#[PrimaryKey]`, and use `#[ForeignKey]` for foreign key columns.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, ForeignKey, PrimaryKey, Table};

#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $name;

    #[Column]
    public string $email;

    #[ForeignKey]
    #[Column]
    public ?string $organisationId;

    #[Column]
    public int $createdOn;
}
```

`#[PrimaryKey]` and `#[ForeignKey]` both extend `#[Column]`, so a property tagged with either is already a mapped column. A model may have a composite primary key by tagging more than one property with `#[PrimaryKey]`.

## Reading records

The `Queryable` trait (mixed into every model) provides static finders.

```php
<?php
declare(strict_types=1);

// By primary key, cached where possible.
$user = User::single('usr_1');           // ?User
$user = User::singleOrFail('usr_1');     // User, throws NotFoundException when missing

// Several primary keys at once.
$users = User::find(['usr_1', 'usr_2']); // ModelArrayList

// A page of all records.
$all = User::all(offset: 0, limit: 20);  // ArrayListInterface

// Existence check without loading the row.
if (User::exists('usr_1')) {
    // ...
}
```

For anything beyond a primary key lookup, start a query with `User::select()` (or `User::query()`) and chain the [query builder](/database/query-builder). Terminal methods hydrate the rows into models.

```php
<?php
declare(strict_types=1);

$active = User::select()
    ->where('is_active', 1)
    ->orderBy('name')
    ->arrayList();
```

## Writing records

Set properties and call `save()`. The ORM inserts a new record or updates an existing one, depending on whether the instance came from the database.

```php
<?php
declare(strict_types=1);

$user = new User();
$user->id = 'usr_3';
$user->name = 'Bas';
$user->email = 'bas@example.com';
$user->save();

$user->name = 'Bas Milius';
$user->save(); // Now an update.
```

`destroy()` deletes the record for the current instance. The static `User::delete($primaryKey)` and `User::update($primaryKey, $values)` operate by primary key without loading the row first.

```php
$user->destroy();

User::delete('usr_3');
User::update('usr_1', ['name' => 'Updated']);
```

## Export and visibility

`toArray()` converts a model to an associative array and `jsonSerialize()` (so `json_encode()`) reuses it. Which properties appear is controlled by attributes and per instance overrides.

- `#[Hidden]` keeps a column, macro or relation out of the export by default.
- `#[Visible]` forces a field to be exported by default.
- `#[Alias]` exports a property under a different key.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Alias, Column, Hidden, PrimaryKey, Table};

#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $name;

    #[Hidden]
    #[Column]
    public string $passwordHash;

    #[Alias('created_at')]
    #[Column]
    public int $createdOn;
}
```

At runtime, `makeHidden()`, `makeVisible()` and `only()` return a clone with adjusted visibility, so the original instance is untouched.

```php
$public = $user->makeHidden('email');
$summary = $user->only(['id', 'name']);
```

## Immutable and computed columns

- `#[Immutable]` blocks writes to a property after the model is hydrated, so a loaded value cannot be changed.
- `#[Computed]` marks a column that the database derives, so it is excluded from insert and update statements while still being read back.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, Computed, Immutable, PrimaryKey, Table};

#[Table('invoices')]
final class Invoice extends Model
{
    #[PrimaryKey]
    #[Immutable]
    #[Column]
    public string $id;

    #[Computed]
    #[Column]
    public string $total;
}
```

## Soft deletes

Add `#[SoftDelete]` to a class to have deletes set a timestamp column instead of removing the row. The column defaults to `deleted_on`. Queries then exclude soft deleted rows unless you opt in with `withDeleted()` on the query.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, PrimaryKey, SoftDelete, Table};

#[Table('documents')]
#[SoftDelete]
final class Document extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public ?int $deletedOn;
}
```

For the relation attributes and how records link together, continue to [relations](/database/orm/relations). For value conversion and embeddables, see [casters, embeddables and polymorphic models](/database/orm/casters-and-embeddables). The full list of attributes lives in the [ORM attributes reference](/database/api/Attributes).

## Custom queryable columns and joins

Every model query passes through two static hooks that a model can override to add extra columns and their supporting joins to every query for that table. They come from `QueryableInterface` and default to a no-op on `Model`:

- `getQueryableColumns(array $columns): array` adds columns (often sub-query or joined columns) to the select.
- `getQueryableJoins(QueryInterface $query): QueryInterface` adds the joins those columns rely on.

Combine them with a `#[Computed]` property to expose a value that lives in another table on every record. The computed property is read back but never written, and the join makes its source column available.

```php
<?php
declare(strict_types=1);

use Override;
use Raxos\Contract\Database\Query\QueryInterface;
use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, Computed, ForeignKey, PrimaryKey, Table};

#[Table('order_line')]
final class OrderLine extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[ForeignKey]
    #[Column]
    public string $productId;

    #[Column]
    #[Computed]
    public string $currency;

    #[Override]
    public static function getQueryableColumns(array $columns): array
    {
        return [
            self::col('*'),
            ...$columns,
            'currency' => Merchant::col('currency'),
        ];
    }

    #[Override]
    public static function getQueryableJoins(QueryInterface $query): QueryInterface
    {
        return $query
            ->join(Product::table(), static fn(QueryInterface $query) => $query
                ->on(Product::col('id'), self::col('product_id')))
            ->join(Merchant::table(), static fn(QueryInterface $query) => $query
                ->on(Merchant::col('id'), Product::col('merchant_id')));
    }
}
```

Now every `OrderLine::select()` (and thus `all()`, `find()`, `single()` and the relation loaders) transparently includes the joined `currency` column.

## Automatic upserts with OnDuplicateUpdate

`#[OnDuplicateUpdate(array|string $fields)]` on the class turns a `save()` insert into an upsert. When `save()` inserts a new record, the ORM appends an `onDuplicateKeyUpdate()` clause for the listed fields, so an insert that hits a duplicate key updates those fields instead of failing.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, OnDuplicateUpdate, PrimaryKey, Table};

#[Table('payment_method')]
#[OnDuplicateUpdate(['external_id', 'name', 'minimum_cents', 'maximum_cents'])]
final class ForeignPaymentMethod extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $externalId;

    #[Column]
    public string $name;

    #[Column]
    public int $minimumCents;

    #[Column]
    public int $maximumCents;
}
```

## Using a non-default connection

By default a model resolves against the default connection. Add `#[ConnectionId(string $connectionId = 'default')]` to the class to bind it to a specific connection instead. The id is the same id the connection was registered under through `Db::register()`, so different models can target different registered connections.

```php
<?php
declare(strict_types=1);

use Raxos\Database\Orm\Model;
use Raxos\Database\Orm\Attribute\{Column, ConnectionId, PrimaryKey, Table};

#[Table('person')]
#[ConnectionId('crm')]
final class Person extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $name;
}
```

See [connections](/database/connections) for how connection ids are registered.

## Visibility across collections

`all()`, `find()` and the collection relations return a [ModelArrayList](/database/api/ModelArrayList). It exposes `makeHidden()`, `makeVisible()` and `only()` too, applying them across every contained model exactly the way `Model` does for a single instance.

```php
$public = User::all()->makeHidden('email');
$summary = User::all()->only(['id', 'name']);
```
