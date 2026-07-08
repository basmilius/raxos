---
outline: deep
---

# Package organization

The interfaces in Contract are grouped by namespace, and each namespace mirrors one Raxos package. Once you know that convention, finding the contract for a given package is quick: the interface for [raxos/database](/database/) lives under `Raxos\Contract\Database`, the one for [raxos/router](/router/) under `Raxos\Contract\Router`, and so on.

## One subdirectory per package

Each subdirectory of `src` corresponds to one Raxos package. Some packages that have a larger surface split their contracts into subfolders, for example `Database` has an `Orm` and a `Query` subfolder, and `Http` has a `Validate` subfolder.

| Namespace | Describes |
|-----------|-----------|
| `Raxos\Contract\Collection` | [raxos/collection](/collection/) |
| `Raxos\Contract\Container` | [raxos/container](/container/) |
| `Raxos\Contract\Database` (with `Orm` and `Query`) | [raxos/database](/database/) |
| `Raxos\Contract\Http` (with `Validate`) | [raxos/http](/http/) |
| `Raxos\Contract\MessageBus` | [raxos/message-bus](/message-bus/) |
| `Raxos\Contract\Router` | [raxos/router](/router/) |
| `Raxos\Contract\Search` | [raxos/search](/search/) |
| `Raxos\Contract\Terminal` | [raxos/terminal](/terminal/) |

The table above is a sample, not the full list. The package also ships contracts for barcode, cache, mail, OpenAPI, rate limit, reflection, security and wallet, each under a matching namespace.

## Root level interfaces

A handful of interfaces live directly under `Raxos\Contract`, because they are used across every package rather than in one specific domain:

- [`ExceptionInterface`](/contract/api/ExceptionInterface): the root of every Raxos exception.
- [`DebuggableInterface`](/contract/api/DebuggableInterface): customizes `var_dump` output.
- [`SerializableInterface`](/contract/api/SerializableInterface): controls PHP serialization.

## Consistent naming

Naming across the package is consistent, which makes contracts easy to guess:

- A contract interface always ends in `Interface`, and its name matches the concrete class it describes. `ConnectionInterface` describes raxos/database's `Connection` class, `RouterInterface` describes raxos/router's `Router` class.
- Many folders contain their own `AttributeInterface`, used by that package's PHP attributes.
- Many folders contain their own `*ExceptionInterface`, the marker interface for that package's exceptions, covered in detail on the [exception contracts](/contract/exceptions) page.
