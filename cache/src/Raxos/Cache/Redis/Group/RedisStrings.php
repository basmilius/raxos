<?php
declare(strict_types=1);

namespace Raxos\Cache\Redis\Group;

use Redis;

/**
 * Trait RedisStrings
 *
 * @property Redis $connection
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Cache\Redis\Group
 * @since 1.0.0
 */
trait RedisStrings
{

    /**
     * Appends the given value to the specified key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function append(string $key, mixed $value): int
    {
        return $this->connection->append($key, $value);
    }

    /**
     * Counts the bits in the specified key.
     *
     * @param string $key
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function bitcount(string $key): int
    {
        return $this->connection->bitCount($key);
    }

    /**
     * Performs bitwise operations between the specified keys.
     *
     * @param string $operation
     * @param string $destinationKey
     * @param string ...$keys
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function bitop(string $operation, string $destinationKey, string ...$keys): int
    {
        return $this->connection->bitOp($operation, $destinationKey, ...$keys);
    }

    /**
     * Finds first bit set or clear in the specified key.
     *
     * @param string $key
     * @param int $bit
     * @param int $start
     * @param int|null $end
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function bitpos(string $key, int $bit, int $start = 0, ?int $end = null): int
    {
        return $this->connection->bitpos($key, $bit, $start, $end);
    }

    /**
     * Decrements the integer value of the specified key.
     *
     * @param string $key
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function decr(string $key): int
    {
        return $this->connection->decr($key);
    }

    /**
     * Decrements the integer value of the specified key by the given amount.
     *
     * @param string $key
     * @param int $amount
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function decrby(string $key, int $amount): int
    {
        return $this->connection->decrBy($key, $amount);
    }

    /**
     * Gets the value of the specified key.
     *
     * @param string $key
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function get(string $key): mixed
    {
        return $this->connection->get($key);
    }

    /**
     * Returns the bit value at the given offset in the string stored
     * at the specified key.
     *
     * @param string $key
     * @param int $offset
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getbit(string $key, int $offset): int
    {
        return $this->connection->getBit($key, $offset);
    }

    /**
     * Gets a substring of the string stored at the specified key.
     *
     * @param string $key
     * @param int $start
     * @param int $end
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getrange(string $key, int $start, int $end): string
    {
        return $this->connection->getRange($key, $start, $end);
    }

    /**
     * Sets the value of the specified key and returns its old value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function getset(string $key, mixed $value): mixed
    {
        return $this->connection->getSet($key, $value);
    }

    /**
     * Increments the integer value of the specified key.
     *
     * @param string $key
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function incr(string $key): int
    {
        return $this->connection->incr($key);
    }

    /**
     * Increments the integer value of the specified key with the given amount.
     *
     * @param string $key
     * @param int $amount
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function incrby(string $key, int $amount): int
    {
        return $this->connection->incrBy($key, $amount);
    }

    /**
     * Increments the float value of the specified key with the given amount.
     *
     * @param string $key
     * @param float $amount
     *
     * @return float
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function incrbyfloat(string $key, float $amount): float
    {
        return $this->connection->incrByFloat($key, $amount);
    }

    /**
     * Gets the values of the specified keys.
     *
     * @param string ...$keys
     *
     * @return array
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function mget(string ...$keys): array
    {
        return $this->connection->mget($keys);
    }

    /**
     * Sets multiple keys to multiple values.
     *
     * @param array $sets
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function mset(array $sets): bool
    {
        return $this->connection->mset($sets);
    }

    /**
     * Sets multiple keys to multiple values, but only if keys are not already stored.
     *
     * @param array $sets
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function msetnx(array $sets): bool
    {
        return $this->connection->msetnx($sets) === 1;
    }

    /**
     * Sets the value and time to live (in milliseconds) of the specified key.
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function psetex(string $key, mixed $value, int $ttl): bool
    {
        return $this->connection->psetex($key, $ttl, $value);
    }

    /**
     * Sets the value of the specified key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function set(string $key, mixed $value): bool
    {
        return $this->connection->set($key, $value);
    }

    /**
     * Sets or clears the bit at offset in the string value stored at the specified key.
     *
     * @param string $key
     * @param int $offset
     * @param bool $value
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function setbit(string $key, int $offset, bool $value): int
    {
        return $this->connection->setBit($key, $offset, $value);
    }

    /**
     * Sets the value of the specified key and also sets the time to live.
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function setex(string $key, mixed $value, int $ttl): bool
    {
        return $this->connection->setex($key, $ttl, $value);
    }

    /**
     * Sets the value of the specified key, but only if the key doesn't exists.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function setnx(string $key, mixed $value): bool
    {
        return $this->connection->setnx($key, $value);
    }

    /**
     * Overwrites part of a string at the specified key starting at the specified offset.
     *
     * @param string $key
     * @param int $offset
     * @param string $value
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function setrange(string $key, int $offset, string $value): int
    {
        return $this->connection->setRange($key, $offset, $value);
    }

    /**
     * Gets the length of the value stored at the specified key.
     *
     * @param string $key
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function strlen(string $key): int
    {
        return $this->connection->strlen($key);
    }

}
