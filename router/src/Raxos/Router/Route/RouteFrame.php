<?php
declare(strict_types=1);

namespace Raxos\Router\Route;

use Raxos\Router\Effect\NotFoundEffect;
use Raxos\Router\Middleware\Middleware;
use Raxos\Router\Response\Response;
use Raxos\Router\Router;
use Raxos\Router\RouterUtil;

/**
 * Class RouteFrame
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Route
 * @since 2.0.0
 */
class RouteFrame
{

    private string $class;
    private string $method;
    private array $middlewares;
    private array $params;
    private array $request;

    /**
     * RouteFrame constructor.
     *
     * @param array $frame
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(array $frame)
    {
        $this->class = $frame['class'];
        $this->method = $frame['method'];
        $this->middlewares = $frame['middlewares'] ?? [];
        $this->params = $frame['params'] ?? [];
        $this->request = $frame['request'];
    }

    /**
     * Invokes the controller method.
     *
     * @param Router $router
     * @param array $params
     *
     * @return mixed
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function invoke(Router $router, array $params): mixed
    {
        foreach ($params as $name => $value) {
            $router->parameter($name, $value);
        }

        foreach ($this->middlewares as [$class, $arguments]) {
            /** @var Middleware $middleware */
            $middleware = new $class($router, ...$arguments);

            $result = $middleware->handle();

            if ($result === true) {
                continue;
            }

            if ($result === false) {
                return new NotFoundEffect($router);
            }

            if ($result instanceof Response) {
                return $result;
            }
        }

        $controller = $router->getControllers()->get($this->class);
        $params = RouterUtil::prepareParameters($router, $this->params, $this->class, $this->method);

        return $controller->invoke($this->method, ...$params);
    }

    /**
     * Prepares the controller.
     *
     * @param Router $router
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function prepareController(Router $router): void
    {
        $controllers = $router->getControllers();

        if ($controllers->has($this->class)) {
            return;
        }

        $controllers->load($router, $this->class);
    }

}
