<?php
declare(strict_types=1);

namespace Raxos\Router\Controller;

use Raxos\Router\Error\ControllerException;
use Raxos\Router\Router;
use Raxos\Router\RouterUtil;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use function sprintf;

/**
 * Class ControllerContainer
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Controller
 * @since 2.0.0
 */
final class ControllerContainer
{

    private array $instances = [];

    /**
     * Gets the given controller instance.
     *
     * @param string $class
     *
     * @return Controller
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public final function get(string $class): Controller
    {
        return $this->instances[$class] ?? throw new ControllerException(sprintf('Instance of controller "%s" not found.', $class), ControllerException::ERR_INSTANCE_NOT_FOUND);
    }

    /**
     * Checks if the given controller is loaded.
     *
     * @param string $class
     *
     * @return bool
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public final function has(string $class): bool
    {
        return isset($this->instances[$class]);
    }

    /**
     * Loads the given controller with the given parameters.
     *
     * @param Router $router
     * @param string $class
     *
     * @return Controller
     * @since 2.0.0
     * @author Bas Milius <bas@glybe.nl>
     */
    public final function load(Router $router, string $class): Controller
    {
        try {
            $reflection = new ReflectionClass($class);
            $parameters = $reflection->getConstructor()->getParameters();
            $params = [];

            foreach ($parameters as $parameter) {
                /** @var ReflectionNamedType $parameterType */
                $parameterType = $parameter->getType();
                $parameterType = $parameterType->getName();

                $param = [
                    'name' => $parameter->getName(),
                    'type' => $parameterType
                ];

                if ($parameter->isDefaultValueAvailable()) {
                    $param['default'] = $parameter->getDefaultValue();
                }

                $params[] = $param;
            }

            $params = RouterUtil::prepareParameters($router, $params, $class);

            return $this->instances[$class] = new $class(...$params);
        } catch (ReflectionException $err) {
            throw new ControllerException(sprintf('Could not initialize controller "%s".', $class), ControllerException::ERR_INITIALIZATION_FAILED, $err);
        }
    }

}
