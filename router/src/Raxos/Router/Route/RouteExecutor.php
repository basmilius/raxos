<?php
declare(strict_types=1);

namespace Raxos\Router\Route;

use Raxos\Router\Effect\Effect;
use Raxos\Router\Effect\ResponseEffect;
use Raxos\Router\Effect\ResultEffect;
use Raxos\Router\Response\Response;
use Raxos\Router\Router;

/**
 * Class RouteExecutor
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Route
 * @since 2.0.0
 */
class RouteExecutor
{

    /** @var RouteFrame[] */
    private array $frames;

    /**
     * RouteExecutor constructor.
     *
     * @param array $frames
     * @param array $params
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(array $frames, private array $params)
    {
        $this->frames = array_map(fn(array $frame): RouteFrame => new RouteFrame($frame), $frames);
    }

    /**
     * Executes the route.
     *
     * @param Router $router
     *
     * @return Effect
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public final function execute(Router $router): Effect
    {
        $result = null;

        foreach ($this->frames as $frame) {
            $result = $this->executeFrame($router, $frame);

            if ($result instanceof Effect) {
                return $result;
            }

            if ($result instanceof Response) {
                return new ResponseEffect($router, $result);
            }
        }

        return new ResultEffect($router, $result);
    }

    /**
     * Executes a single route frame.
     *
     * @param Router $router
     * @param RouteFrame $frame
     *
     * @return mixed
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function executeFrame(Router $router, RouteFrame $frame): mixed
    {
        $frame->prepareController($router);

        return $frame->invoke($router, $this->params);
    }

}
