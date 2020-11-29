<?php
declare(strict_types=1);

namespace Raxos\Router\Effect;

use Raxos\Router\Router;

/**
 * Class Effect
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Effect
 * @since 2.0.0
 */
abstract class Effect
{

    /**
     * Effect constructor.
     *
     * @param Router $router
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(protected Router $router)
    {
    }

}
