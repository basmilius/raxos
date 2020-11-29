<?php
declare(strict_types=1);

namespace Raxos\Router\Middleware;

use Raxos\Router\Effect\Effect;
use Raxos\Router\Response\Response;
use Raxos\Router\Response\ResponseMethods;
use Raxos\Router\Router;

/**
 * Class Middleware
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Middleware
 * @since 2.0.0
 */
abstract class Middleware
{

    use ResponseMethods;

    /**
     * Middleware constructor.
     *
     * @param Router $router
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(protected Router $router)
    {
    }

    /**
     * Handles the request.
     *
     * @return Effect|Response|bool|null
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public abstract function handle(): Effect|Response|bool|null;

}
