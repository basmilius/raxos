---
outline: deep
---

# Select options

The `#[SelectOption]` attribute describes a model as a type-ahead source, separate from the filter and policy search flow. Where a full search ranks results by a combined score, select options are meant for a dropdown: a substring match over a small set of columns, ordered and limited for a picker component.

## Declaring select options

`#[SelectOption]` is declared once per model:

```php
use Raxos\Database\Orm\Attribute\{Column, PrimaryKey, Table};
use Raxos\Database\Orm\Model;
use Raxos\Search\Attribute\SelectOption;

#[Table('users')]
#[SelectOption(
    searchKeys: ['name', 'email'],
    order: 'name',
    descending: false,
    limit: 25,
    emptyLimit: 10
)]
final class User extends Model
{
    #[PrimaryKey]
    #[Column]
    public int $id;

    #[Column]
    public string $name;

    #[Column]
    public string $email;
}
```

## Options

| Parameter    | Type            | Default | Meaning                                                      |
|--------------|-----------------|---------|--------------------------------------------------------------|
| `searchKeys` | `string[]`      | -       | Columns matched with a substring `LIKE` search.              |
| `order`      | `?string`       | `null`  | Order-by column. Defaults to the first search key.           |
| `descending` | `bool`          | `false` | Whether to order descending.                                 |
| `limit`      | `int`           | `25`    | Maximum number of results when a search term is present.     |
| `emptyLimit` | `?int`          | `null`  | Maximum results with no search term. Defaults to `limit`.    |

The `searchKeys` are matched with a substring `LIKE` search, which suits type-ahead dropdowns rather than ranked search. When `order` is null it falls back to the first search key, and when `emptyLimit` is null it falls back to `limit`.

## Configuration only

The attribute only carries configuration. Reading it with reflection and building the query from it is left to the application, so you decide how the type-ahead endpoint uses the search keys, ordering and limits. This keeps select options independent of the [filter](/search/filters) and [policy](/search/policies) pipeline that `SearchProvider` drives.
