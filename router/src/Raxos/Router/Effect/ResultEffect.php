<?php
declare(strict_types=1);

namespace Raxos\Router\Effect;

use Raxos\Router\Router;

/**
 * Class ResultEffect
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Effect
 * @since 2.0.0
 */
final class ResultEffect extends Effect
{

    /**
     * ResultEffect constructor.
     *
     * @param Router $router
     * @param mixed $result
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(Router $router, private mixed $result)
    {
        parent::__construct($router);
    }

    /**
     * Gets the result.
     *
     * @return mixed
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public final function getResult(): mixed
    {
        return $this->result;
    }

}
