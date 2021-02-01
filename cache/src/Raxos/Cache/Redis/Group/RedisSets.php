<?php
declare(strict_types=1);

namespace Raxos\Cache\Redis\Group;

use Redis;

/**
 * Trait RedisSets
 *
 * @property Redis $connection
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Cache\Redis\Group
 * @since 1.0.0
 */
trait RedisSets
{

    /**
     * Adds the given members to the set at the specified key.
     *
     * @param string $key
     * @param string ...$members
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sadd(string $key, string ...$members): int
    {
        return $this->connection->sAdd($key, ...$members);
    }

    /**
     * Gets the number of members in the set at the specified key.
     *
     * @param string $key
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function scard(string $key): int
    {
        return $this->connection->sCard($key);
    }

    /**
     * Subtract the sets at the specified keys.
     *
     * @param string ...$keys
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sdiff(string ...$keys): array
    {
        return $this->connection->sDiff(...$keys);
    }

    /**
     * Subtract the sets at the specified keys and store the result in
     * the specified destination key.
     *
     * @param string $destination
     * @param string ...$keys
     *
     * @return int|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sdiffstore(string $destination, string ...$keys): ?int
    {
        return $this->connection->sDiffStore($destination, ...$keys) ?: null;
    }

    /**
     * Intersect the sets at the specified keys.
     *
     * @param string ...$keys
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sinter(string ...$keys): array
    {
        return $this->connection->sInter(...$keys);
    }

    /**
     * Intersect the sets at the specified keys and stores the result in
     * the specified destination key.
     *
     * @param string $destination
     * @param string ...$keys
     *
     * @return int|null
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sinterstore(string $destination, string ...$keys): ?int
    {
        return $this->connection->sInterStore($destination, ...$keys) ?: null;
    }

    /**
     * Determine if the given member is part of the set at the specified key.
     *
     * @param string $key
     * @param string $member
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sismember(string $key, string $member): bool
    {
        return $this->connection->sIsMember($key, $member);
    }

    /**
     * Gets all members in the set at the specified key.
     *
     * @param string $key
     *
     * @return string[]
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function smembers(string $key): array
    {
        return $this->connection->sMembers($key);
    }

    /**
     * Moves a member from the set at the given source to the set at the
     * given destination.
     *
     * @param string $source
     * @param string $destination
     * @param string $member
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function smove(string $source, string $destination, string $member): bool
    {
        return $this->connection->sMove($source, $destination, $member);
    }

    /**
     * Removes and returns one or multiple random members from the set at
     * the specified key.
     *
     * @param string $key
     * @param int $count
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function spop(string $key, int $count = 1): mixed
    {
        return $this->connection->sPop($key, $count);
    }

    /**
     * Returns one or multiple random members from the set at the specified key.
     *
     * @param string $key
     * @param int $count
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function srandmember(string $key, int $count = 1): mixed
    {
        return $this->connection->sRandMember($key, $count);
    }

    /**
     * Removes the given members from the set at the specified key.
     *
     * @param string $key
     * @param string ...$members
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function srem(string $key, string ...$members): int
    {
        return $this->connection->sRem($key, ...$members);
    }

    /**
     * Adds multiple sets.
     *
     * @param string ...$keys
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sunion(string ...$keys): array
    {
        return $this->connection->sUnion(...$keys);
    }

    /**
     * Adds multiple sets and stores the result in the specified destination key.
     *
     * @param string $destination
     * @param string ...$keys
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function sunionstore(string $destination, string ...$keys): int
    {
        return $this->connection->sUnionStore($destination, ...$keys);
    }

}
