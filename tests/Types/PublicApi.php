<?php
declare(strict_types=1);

use Raxos\Collection\ArrayList;
use Raxos\Collection\LazySequence;
use Raxos\Collection\Map;
use Raxos\Collection\ReadonlyMap;
use Raxos\Container\Container;
use Raxos\Http\Client\HttpClient;
use function PHPStan\Testing\assertType;

$map = new Map(['count' => 42]);
assertType('int|null', $map->get('missing'));
assertType("'fallback'|int", $map->get('missing', 'fallback'));
assertType('int|null', new ReadonlyMap(['count' => 42])->get('missing'));

$list = ArrayList::of([1, 2]);
assertType('Raxos\Collection\ArrayList<int, int>', $list);
assertType('Raxos\Collection\LazySequence<int, int>', LazySequence::from([1, 2]));
assertType('Raxos\Collection\LazySequence<int, decimal-int-string>', LazySequence::from([1, 2])->map(static fn(int $value): string => (string)$value));
assertType('stdClass', new Container()->get(stdClass::class));
assertType('Raxos\Http\Client\HttpClientRequest', new HttpClient()->request());
