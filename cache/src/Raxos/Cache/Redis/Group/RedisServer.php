<?php
declare(strict_types=1);

namespace Raxos\Cache\Redis\Group;

use Redis;

/**
 * Trait RedisServer
 *
 * @property Redis $connection
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Cache\Redis\Group
 * @since 1.0.0
 */
trait RedisServer
{

    /**
     * Removes all information from all databases.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function flushAll(): bool
    {
        return $this->connection->flushAll();
    }

    /**
     * Removes all information from the current database.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function flushDatabase(): bool
    {
        return $this->connection->flushDB();
    }

}
