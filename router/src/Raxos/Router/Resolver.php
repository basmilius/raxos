<?php
declare(strict_types=1);

namespace Raxos\Router;

use Raxos\Router\Attribute\Get;
use Raxos\Router\Attribute\Prefix;
use Raxos\Router\Attribute\Route;
use Raxos\Router\Attribute\SubController;
use Raxos\Router\Attribute\With;
use Raxos\Router\Controller\Controller;
use Raxos\Router\Error\RegisterException;
use Raxos\Router\Route\RouteExecutor;
use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use function array_filter;
use function array_merge;
use function in_array;
use function is_string;
use function is_subclass_of;
use function preg_match;
use function rtrim;
use function sprintf;
use function strlen;
use function strtr;
use function uksort;
use const ARRAY_FILTER_USE_KEY;

/**
 * Class Resolver
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router
 * @since 2.0.0
 */
class Resolver
{

    private const ARRAYABLE_OPTIONS = ['middlewares', 'request'];

    private array $callStack = [];
    private array $controllerList = [];
    private array $mappings = [];
    private array $resolverDidControllers = [];

    /**
     * Adds the given controller.
     *
     * @param Router $router
     * @param Controller|string $controller
     *
     * @throws RegisterException
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected function addController(Router $router, Controller|string $controller): void
    {
        if (!is_subclass_of($controller, Controller::class)) {
            throw new RegisterException(sprintf('Argument 1 to %s must be an instance or subclass of %s.', __METHOD__, Controller::class), RegisterException::ERR_NOT_A_CONTROLLER);
        }

        if (is_string($controller)) {
            $controller = new $controller($router);
        }

        $this->controllerList[] = $controller;
    }

    /**
     * Resolves the call stack for each route from the mappings.
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected function resolveCallStack(): void
    {
        $frames = [];

        foreach ($this->mappings as $controller) {
            $frames = array_merge($frames, $this->resolveCallStackController($controller));
        }

        uksort($frames, fn(string $a, string $b): int => strlen($b) <=> strlen($a));

        $this->callStack = $frames;
    }

    /**
     * Resolves the controller mappings.
     *
     * @throws RegisterException
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected function resolveMappings(): void
    {
        $this->resolverDidControllers = [];

        try {
            foreach ($this->controllerList as $controller) {
                $class = new ReflectionClass($controller);
                $mapping = $this->resolveControllerMapping($class);

                if ($mapping === null) {
                    continue;
                }

                $this->mappings[] = $mapping;
            }
        } catch (ReflectionException $err) {
            throw new RegisterException('Could not map controllers because of an reflection error.', RegisterException::ERR_MAPPING_FAILED, $err);
        }
    }

    /**
     * Resolves the request into a route, and returns null if nothing is found.
     *
     * @param string $method
     * @param string $path
     *
     * @return RouteExecutor|null
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected function resolveRequest(string $method, string $path): ?RouteExecutor
    {
        foreach ($this->callStack as $route => $requestMethods) {
            $frames = $requestMethods[$method] ?? $requestMethods['any'] ?? null;
            $regex = "#^{$route}$#";

            if ($frames === null) {
                continue;
            }

            if (!preg_match($regex, $path, $matches)) {
                continue;
            }

            $params = array_filter($matches, fn(string|int $key): bool => is_string($key), ARRAY_FILTER_USE_KEY);

            return new RouteExecutor($frames, $params);
        }

        return null;
    }

    /**
     * Converts the given attribute to an option.
     *
     * @param ReflectionAttribute $attribute
     *
     * @return array|null
     * @throws ReflectionException
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function convertAttribute(ReflectionAttribute $attribute): ?array
    {
        switch ($attribute->getName()) {
            case Get::class:
            case Route::class:
                /** @var Route $attr */
                $attr = $attribute->newInstance();

                return ['request', [$attr->getMethod(), $attr->getPath()]];

            case Prefix::class:
                /** @var Prefix $attr */
                $attr = $attribute->newInstance();

                return ['prefix', $attr->getPath()];

            case SubController::class:
                /** @var SubController $attr */
                $attr = $attribute->newInstance();

                return ['child', $this->resolveControllerMapping(new ReflectionClass($attr->getClass()))];

            case With::class:
                /** @var With $attr */
                $attr = $attribute->newInstance();

                return ['middlewares', [$attr->getClass(), $attr->getArguments()]];

            default:
                return null;
        }
    }

    /**
     * Converts the given attributes to options.
     *
     * @param ReflectionAttribute[] $attributes
     * @param array $options
     *
     * @throws ReflectionException
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function convertAttributes(array $attributes, array &$options): void
    {
        foreach ($attributes as $attribute) {
            $result = $this->convertAttribute($attribute);

            if ($result === null) {
                continue;
            }

            if (in_array($result[0], self::ARRAYABLE_OPTIONS)) {
                $options[$result[0]][] = $result[1];
            } else {
                $options[$result[0]] = $result[1];
            }
        }
    }

    /**
     * Converts the request path to regex with the given params.
     *
     * @param array $request
     * @param array $params
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function convertPath(array &$request, array $params): void
    {
        $path = $request[1];

        foreach ($params as $param) {
            if (!in_array($param['type'], RouterUtil::SIMPLE_TYPES)) {
                continue;
            }

            $regex = $this->convertPathParam($param['name'], $param['type'], isset($param['default']));

            $path = strtr($path, [
                '/$' . $param['name'] => $regex,
                '.$' . $param['name'] => $regex
            ]);
        }

        $request[] = $path;
    }

    /**
     * Converts the given param to regex.
     *
     * @param string $name
     * @param string $type
     * @param bool $defaultValue
     *
     * @return string
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function convertPathParam(string $name, string $type, bool $defaultValue): string
    {
        $regex = match ($type) {
            'string' => '[a-zA-Z0-9-_.@=,]+',
            'int' => '[0-9]+',
            'bool' => '(1|0|true|false)',
            default => ''
        };

        $prefix = '[/.]' . ($defaultValue ? '?' : '');

        return "{$prefix}(?<{$name}>{$regex})";
    }

    /**
     * Resolves the call stack for a single controller.
     *
     * @param array $controller
     * @param string|null $prefix
     * @param array $parents
     *
     * @return array
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function resolveCallStackController(array $controller, ?string $prefix = null, array $parents = []): array
    {
        $frames = [];
        $prefix = $controller['prefix'] ?? $prefix ?? '';

        if ($prefix === '/') {
            $prefix = '';
        }

        foreach ($controller['routes'] as $route) {
            $frames = array_merge($frames, $this->resolveCallStackRoute($controller, $route, $prefix, $parents));
        }

        return $frames;
    }

    /**
     * Resolves the call stack for a single controller method.
     *
     * @param array $controller
     * @param array $route
     * @param string $prefix
     * @param array $parents
     *
     * @return array
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function resolveCallStackRoute(array $controller, array $route, string $prefix, array $parents = []): array
    {
        $frames = [];

        $frame = [
            'class' => $route['class'],
            'method' => $route['method']
        ];

        if (isset($controller['middlewares']) || isset($route['middlewares'])) {
            $frame['middlewares'] = [
                ...($controller['middlewares'] ?? []),
                ...($route['middlewares'] ?? [])
            ];
        }

        if (isset($route['params'])) {
            $frame['params'] = $route['params'];
        }

        foreach ($route['request'] as [$requestMethod, $path, $regex]) {
            $routeCall = array_merge($frame, [
                'request' => [$requestMethod, $path],
            ]);

            $routePath = $prefix . $regex;

            if ($routePath !== '/') {
                $routePath = rtrim($routePath, '/');
            }

            if (isset($route['child'])) {
                $frames = array_merge($frames, $this->resolveCallStackController($route['child'], $routePath, [...$parents, $routeCall]));
            } else {
                $frames[$routePath][$requestMethod] ??= [];
                $frames[$routePath][$requestMethod] = array_merge($frames[$routePath][$requestMethod], $parents);
                $frames[$routePath][$requestMethod][] = $routeCall;
            }
        }

        return $frames;
    }

    /**
     * Resolves the mappings for a single controller.
     *
     * @param ReflectionClass $class
     *
     * @return array|null
     * @throws ReflectionException
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function resolveControllerMapping(ReflectionClass $class): ?array
    {
        if (in_array($class->getName(), $this->resolverDidControllers)) {
            throw new RegisterException(sprintf('Controller class "%s" can only be used once.', $class->getName()), RegisterException::ERR_RECURSION_DETECTED);
        }

        $this->resolverDidControllers[] = $class->getName();

        $controllerAttributes = $class->getAttributes();
        $controllerMethods = $class->getMethods();

        $mapping = [
            'name' => $class->getName(),
            'routes' => []
        ];

        $this->convertAttributes($controllerAttributes, $mapping);

        foreach ($controllerMethods as $method) {
            $methodMapping = $this->resolveMethodMapping($class, $method);

            if ($methodMapping === null) {
                continue;
            }

            $mapping['routes'][] = $methodMapping;
        }

        if (empty($mapping['routes'])) {
            return null;
        }

        return $mapping;
    }

    /**
     * Resolves the mappings for a single controller method.
     *
     * @param ReflectionClass $class
     * @param ReflectionMethod $method
     *
     * @return array|null
     * @throws ReflectionException
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    private function resolveMethodMapping(ReflectionClass $class, ReflectionMethod $method): ?array
    {
        $methodAttributes = $method->getAttributes();

        $mapping = [
            'class' => $class->getName(),
            'method' => $method->getName(),
            'request' => null
        ];

        $this->convertAttributes($methodAttributes, $mapping);

        if ($mapping['request'] === null) {
            return null;
        }

        if (!$method->hasReturnType()) {
            throw new RegisterException(sprintf('Method "%s::%s()" should have a return type.', $class->getName(), $method->getName()), RegisterException::ERR_MISSING_TYPE);
        }

        /** @var ReflectionNamedType $returnType */
        $returnType = $method->getReturnType();
        $returnType = $returnType->getName();

        if (isset($mapping['child']) && $returnType !== 'void') {
            throw new RegisterException(sprintf('The return type of method "%s::%s()" should be void.', $class->getName(), $method->getName()), RegisterException::ERR_MISSING_TYPE);
        }

        if ($method->getNumberOfParameters() > 0) {
            $params = [];
            $parameters = $method->getParameters();

            foreach ($parameters as $parameter) {
                $params[] = $this->resolveParameterMapping($class, $method, $parameter);
            }

            $mapping['params'] = $params;
        }

        foreach ($mapping['request'] as &$request) {
            $this->convertPath($request, $mapping['params'] ?? []);
        }

        return $mapping;
    }

    /**
     * Resolves the mappings for a single parameter of a single controller method.
     *
     * @param ReflectionClass $class
     * @param ReflectionMethod $method
     * @param ReflectionParameter $parameter
     *
     * @return array
     * @throws ReflectionException
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    #[ArrayShape([
        'name' => 'string',
        'type' => 'string',
        'default' => 'mixed'
    ])]
    private function resolveParameterMapping(ReflectionClass $class, ReflectionMethod $method, ReflectionParameter $parameter): array
    {
        if (!$parameter->hasType()) {
            throw new RegisterException(sprintf('Parameter "%s" of method "%s::%s" should be strongly typed.', $parameter->getName(), $class->getName(), $method->getName()), RegisterException::ERR_MISSING_TYPE);
        }

        /** @var ReflectionNamedType $type */
        $type = $parameter->getType();

        $param = [
            'name' => $parameter->getName(),
            'type' => $type->getName()
        ];

        if ($parameter->isDefaultValueAvailable()) {
            $param['default'] = $parameter->getDefaultValue();
        }

        return $param;
    }

}
