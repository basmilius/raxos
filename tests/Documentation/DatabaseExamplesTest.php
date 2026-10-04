<?php
declare(strict_types=1);

use Raxos\Database\Query\Query;
use function RaxosTests\Database\unitConnection;

covers(Query::class);

it('executes the published query-builder examples against real records', function (string $driver, string $section): void {
    $connection = unitConnection($driver);
    $connection->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, name VARCHAR(100), email VARCHAR(100), country VARCHAR(10), is_active INTEGER)');
    $connection->execute('CREATE TABLE orders (id INTEGER PRIMARY KEY, status VARCHAR(100), total INTEGER)');
    $connection->execute('CREATE TABLE profiles (user_id INTEGER, bio VARCHAR(100))');

    try {
        for ($id = 1; $id <= 14; ++$id) {
            $connection->query()->insertIntoValues('users', ['id' => $id, 'name' => sprintf('User %02d', $id), 'email' => 'user@example.org', 'country' => $id === 2 ? 'BE' : 'NL', 'is_active' => $id === 2 ? 0 : 1])->run();
        }
        $connection->execute("INSERT INTO orders VALUES (1, 'paid', 50), (2, 'paid', 150), (3, 'refunded', 10), (4, 'pending', 200)");
        $connection->execute("INSERT INTO profiles VALUES (1, 'First profile'), (2, 'Second profile')");
        $docs = file_get_contents(dirname(__DIR__, 2) . '/docs/database/query-builder.md');
        expect(preg_match('/## ' . preg_quote($section, '/') . '\n.*?```php\n(.*?)```/s', $docs, $match))->toBe(1);
        $code = preg_replace('/^<\?php\s*/', '', $match[1]);
        $rows = eval($code . "\nreturn \$rows;");
        match ($section) {
            'Selecting' => expect(array_column($rows, 'id'))->toBe([1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]),
            'Where clauses' => expect(array_column($rows, 'id'))->toBe([2, 3]),
            'Joins' => expect($rows[0])->toBe(['id' => 1, 'bio' => 'First profile'])->and($rows[2]['bio'])->toBeNull(),
            'Grouping and ordering' => expect($rows)->toBe([['country' => 'NL', 'count' => 13]]),
        };
    } finally {
        $connection->execute('DROP TABLE profiles');
        $connection->execute('DROP TABLE orders');
        $connection->execute('DROP TABLE users');
    }
})->with(['sqlite', 'mysql', 'mariadb'])->with(['Selecting', 'Where clauses', 'Joins', 'Grouping and ordering']);
