<?php
declare(strict_types=1);

namespace Raxos\Cache\Redis;

use Raxos\Cache\Error\CacheException;

/**
 * Class RedisCacheException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Cache\Redis
 * @since 1.0.0
 */
final class RedisCacheException extends CacheException
{

    public const ERR_DATABASE_SELECT_FAILED = 1;
    public const ERR_INVALID_CALL = 2;

}
