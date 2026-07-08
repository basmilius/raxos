---
outline: deep

cards:
    highlights:
        -   title: Db
            code: true
            details: 'Register connections by id and reach the current one from anywhere.'
            link: /database/api/Db
        -   title: Connection
            code: true
            details: 'MySql, MariaDb and SQLite drivers behind one PDO backed interface.'
            link: /database/api/Connection
        -   title: Query
            code: true
            details: 'Compose select, insert, update and delete statements fluently.'
            link: /database/api/Query
        -   title: Model
            code: true
            details: 'Map rows to attribute driven models with relations and casters.'
            link: /database/api/Model
        -   title: Expr
            code: true
            details: 'Build comparison, aggregate, date and string SQL expressions.'
            link: /database/api/Expr
        -   title: ORM attributes
            code: true
            details: 'Describe tables, columns, keys, relations and embeddables.'
            link: /database/api/Attributes
---

# Database

Raxos Database provides a fluent, attribute driven ORM together with a low level SQL query builder. Connections wrap PDO for MySQL, MariaDB and SQLite behind one interface, the query builder composes select, insert, update and delete statements piece by piece, and the ORM layer maps rows to model classes using PHP attributes for columns, primary keys, casters, embeddables and relations (has one, has many, belongs to, belongs to many, and their "through" and polymorphic variants). A query logger, a per request model identity cache and nested transactions with savepoints round out the package.

The three layers (connections, query builder and ORM) are separate but composable: you can drop down to raw SQL through a connection, build a query without ever touching a model, or work entirely through models and let the ORM assemble the queries for you.

## Highlights

<LinkCards group="highlights"/>

## Explore by category

- [Connections](/database/connections): create and register MySql, MariaDb and SQLite connections and resolve them through the `Db` facade.
- [Query builder](/database/query-builder): build select, insert, update and delete queries with joins, where clauses and expressions.
- [Transactions, caching and logging](/database/transactions-and-logging): nested transactions with savepoints, the model identity cache and the query logger.
- [Models](/database/orm/models): define models with `#[Table]`, `#[Column]` and `#[PrimaryKey]`, then read, save and delete records.
- [Relations](/database/orm/relations): declare relations with attributes and control eager loading.
- [Casters, embeddables and polymorphic models](/database/orm/casters-and-embeddables): convert column values, embed value objects and map single table inheritance.

## Quick example

```php
<?php
declare(strict_types=1);

use Raxos\Database\Connection\MySql;
use Raxos\Database\Db;
use Raxos\Database\Orm\{Model, ModelArrayList};
use Raxos\Database\Orm\Attribute\{Column, HasMany, PrimaryKey, Table};
use function Raxos\Database\Query\literal;

#[Table('users')]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public string $id;

    #[Column]
    public string $name;

    #[HasMany(Post::class)]
    public ModelArrayList $posts;
}

Db::register(MySql::createFromOptions(
    host: 'localhost',
    database: 'app',
    username: 'root',
));

$user = User::singleOrFail('usr_123');
$user->name = 'Bas';
$user->save();

$recent = User::select()
    ->where('created_on', '>', literal('now() - interval 7 day'))
    ->arrayList();
```

This registers a MySQL connection, defines a model with a primary key, a plain column and a has many relation, then reads and writes through it.

## Installation

Install the package with Composer. See [installation](/database/installation) for the required PHP version and extensions.

```shell
composer require raxos/database
```
