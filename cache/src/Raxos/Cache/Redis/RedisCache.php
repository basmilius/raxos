<?php
declare(strict_types=1);

namespace Raxos\Cache\Redis;

use Raxos\Cache\Redis\Group\{RedisKeys, RedisPubSub, RedisServer, RedisSets, RedisStrings};
use Redis;
use function sprintf;

/**
 * Class RedisCache
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Cache\Redis
 * @since 1.0.0
 */
class RedisCache
{

    use RedisKeys;
    use RedisPubSub;
    use RedisServer;
    use RedisSets;
    use RedisStrings;

    protected Redis $connection;

    /**
     * RedisCache constructor.
     *
     * @param string $prefix
     * @param string $host
     * @param int $port
     * @param float $timeout
     * @param bool $connect
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        protected string $prefix,
        protected string $host = '127.0.0.1',
        protected int $port = 6379,
        protected float $timeout = 0.0,
        bool $connect = true
    )
    {
        $this->connection = new Redis();

        if ($connect) {
            $this->connect();
        }
    }

    /**
     * Connects to the Redis server.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function connect(): bool
    {
        return $this->connection->connect($this->host, $this->port, $this->timeout);
    }

    /**
     * Remembers data in our cache.
     *
     * @template T
     *
     * @param string $key
     * @param int $ttl
     * @param callable():T $fn
     *
     * @return T
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function remember(string $key, int $ttl, callable $fn)
    {
        if ($this->exists($key)) {
            return $this->get($key);
        }

        $this->setex($key, $value = $fn(), $ttl);

        return $value;
    }

    /**
     * Selects the given database.
     *
     * @param int $databaseId
     *
     * @throws RedisCacheException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function selectDatabase(int $databaseId): void
    {
        if ($this->connection->select($databaseId) === false) {
            throw new RedisCacheException(sprintf('Could not select database with id %d.', $databaseId), RedisCacheException::ERR_DATABASE_SELECT_FAILED);
        }
    }

    /**
     * Gets a tagged cache instance.
     *
     * @param string[] $tags
     *
     * @return RedisTaggedCache
     * @throws RedisCacheException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function tags(array $tags): RedisTaggedCache
    {
        return new RedisTaggedCache($this, $tags);
    }

    /**
     * Gets the Redis connection.
     *
     * @return Redis
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getConnection(): Redis
    {
        return $this->connection;
    }

    /**
     * Gets the server host.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getHost(): string
    {
        return $this->host;
    }

    /**
     * Gets the server port.
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getPort(): int
    {
        return $this->port;
    }

    /**
     * Gets the key prefix.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Gets the timeout.
     *
     * @return float
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * Returns TRUE if we're connected to a Redis server.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function isConnected(): bool
    {
        return $this->connection->isConnected();
    }

}
