<?php
declare(strict_types=1);

namespace Raxos\Cache\Redis\Group;

use Redis;

/**
 * Trait RedisKeys
 *
 * @property Redis $connection
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Cache\Redis\Group
 * @since 1.0.0
 */
trait RedisKeys
{

    /**
     * Delete one or more keys.
     *
     * @param string ...$keys
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function del(string ...$keys): bool
    {
        return $this->connection->del($keys) > 0;
    }

    /**
     * Returns a serialized version of the value stored at the specified key.
     *
     * @param string $key
     *
     * @return string|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function dump(string $key): ?string
    {
        return $this->connection->dump($key) ?: null;
    }

    /**
     * Determine if the specified key exists.
     *
     * @param string $key
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function exists(string $key): bool
    {
        return $this->connection->exists($key) > 0;
    }

    /**
     * Sets the specified key's time to live in seconds.
     *
     * @param string $key
     * @param int $seconds
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function expire(string $key, int $seconds = 0): bool
    {
        return $this->connection->expire($key, $seconds);
    }

    /**
     * Sets the specified key's expiration timestamp.
     *
     * @param string $key
     * @param int $unixTimestamp
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function expireAt(string $key, int $unixTimestamp): bool
    {
        return $this->connection->expireAt($key, $unixTimestamp);
    }

    /**
     * Find all keys that match the given pattern. If no pattern was specified
     * all keys are returned.
     *
     * @param string $pattern
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function keys(string $pattern = '*'): array
    {
        return $this->connection->keys($pattern);
    }

    /**
     * Atomically transfer the specified key from a Redis instance to another one.
     *
     * @param string $host
     * @param int $port
     * @param string $key
     * @param int $database
     * @param float $timeout
     * @param bool $copy
     * @param bool $replace
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function migrate(string $host, int $port, string $key, int $database, float $timeout, bool $copy = false, bool $replace = false): bool
    {
        return $this->connection->migrate($host, $port, $key, $database, $timeout, $copy, $replace);
    }

    /**
     * Moves the specified key to the specified database.
     *
     * @param string $key
     * @param int $database
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function move(string $key, int $database): bool
    {
        return $this->connection->move($key, $database);
    }

    /**
     * Inspect the internals of Redis objects.
     *
     * @param string $command
     * @param string $key
     *
     * @return bool|int|string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function object(string $command, string $key): bool|int|string
    {
        return $this->connection->object($command, $key);
    }

    /**
     * Removes the expiration from the specified key.
     *
     * @param string $key
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function persist(string $key): bool
    {
        return $this->connection->persist($key);
    }

    /**
     * Sets the specified key's time to live in milliseconds.
     *
     * @param string $key
     * @param int $milliseconds
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function pexpire(string $key, int $milliseconds): bool
    {
        return $this->connection->pExpire($key, $milliseconds);
    }

    /**
     * Sets the specified key's expiration timestamp in milliseconds.
     *
     * @param string $key
     * @param $unixTimestampMs
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function pexpireat(string $key, $unixTimestampMs): bool
    {
        return $this->connection->pExpireAt($key, $unixTimestampMs);
    }

    /**
     * Gets the time to live for the specified key in milliseconds.
     *
     * @param string $key
     *
     * @return int|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function pttl(string $key): ?int
    {
        return $this->connection->pttl($key) ?: null;
    }

    /**
     * Returns a random key from the keyspace.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function randomkey(): string
    {
        return $this->connection->randomKey();
    }

    /**
     * Renames the specified key.
     *
     * @param string $key
     * @param string $newKey
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function rename(string $key, string $newKey): bool
    {
        return $this->connection->rename($key, $newKey);
    }

    /**
     * Renames the specified key, but only if the new name doesn't already exixts.
     *
     * @param string $key
     * @param string $newKey
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function renamenx(string $key, string $newKey): bool
    {
        return $this->connection->renameNx($key, $newKey);
    }

    /**
     * Creates the specified key using the provided serialized value, that was
     * previously obtained using {@see FeatureGroupKeys::dump()}.
     *
     * @param string $key
     * @param int $ttl
     * @param string $serialized
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function restore(string $key, int $ttl, string $serialized): bool
    {
        return $this->connection->restore($key, $ttl, $serialized);
    }

    /**
     * Sorts the elements in a list, set or sorted set.
     *
     * @param string $key
     * @param array|null $options
     *
     * @return array
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sort(string $key, ?array $options = null): array
    {
        return $this->connection->sort($key, $options);
    }

    /**
     * Alters the last access time of the specified keys.
     *
     * @param string ...$keys
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function touch(string ...$keys): bool
    {
        return $this->connection->rawCommand('TOUCH', $keys) > 0;
    }

    /**
     * Gets the time to live of the specified key.
     *
     * @param string $key
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function ttl(string $key): int
    {
        return $this->connection->ttl($key);
    }

    /**
     * Determine the type of the value of the specified key.
     *
     * @param string $key
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function type(string $key): int
    {
        return $this->connection->type($key);
    }

    /**
     * Deletes the specified keys asynchronously in another thread. Otherwise
     * it's just as {@see FeatureGroupKeys::del()}, but non blocking.
     *
     * @param string ...$keys
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function unlink(string ...$keys): bool
    {
        return $this->connection->unlink(...$keys) > 0;
    }

    /**
     * Wait for the synchronous replication of all the write commands sent
     * in the context of the current connection.
     *
     * @param int $replicas
     * @param int $timeout
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function wait(int $replicas, int $timeout): int
    {
        return $this->connection->wait($replicas, $timeout);
    }

}
