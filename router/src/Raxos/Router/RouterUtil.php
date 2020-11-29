<?php
declare(strict_types=1);

namespace Raxos\Router;

use JetBrains\PhpStorm\Pure;
use Raxos\Router\Error\ControllerException;
use function in_array;
use function intval;
use function strval;

/**
 * Class RouterUtil
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router
 * @since 2.0.0
 */
final class RouterUtil
{

    public const SIMPLE_TYPES = ['string', 'bool', 'int'];

    /**
     * Converts the given value to the correct type.
     *
     * @param string $type
     * @param string $value
     *
     * @return string|int|bool|null
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    #[Pure]
    public static function convertParameterType(string $type, string $value): string|int|bool|null
    {
        return match ($type) {
            'string' => strval($value),
            'int' => intval($value),
            'bool' => $value === '1' || $value === 'true',
            default => null
        };
    }

    /**
     * Prepares the parameters for a controller or controller method.
     *
     * @param Router $router
     * @param array $parameters
     * @param string $controller
     * @param string|null $method
     *
     * @return array
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public static function prepareParameters(Router $router, array $parameters, string $controller, ?string $method = null): array
    {
        $params = [];

        foreach ($parameters as $parameter) {
            $parameterName = $parameter['name'];
            $parameterType = $parameter['type'];

            if ($router->hasParameter($parameterName)) {
                $value = $router->getParameter($parameterName);

                if (in_array($parameterType, self::SIMPLE_TYPES)) {
                    $value = self::convertParameterType($parameterType, $value);
                }

                $params[] = $value;
            } else if (isset($parameter['default'])) {
                $params[] = $parameter['default'];
            } else {
                if ($method !== null) {
                    throw new ControllerException(sprintf('Could not invoke controller method "%s::%s()", missing parameter "%s" with type "%s".', $controller, $method, $parameterName, $parameterType), ControllerException::ERR_INITIALIZATION_FAILED);
                } else {
                    throw new ControllerException(sprintf('Could not initialize controller "%s", missing parameter "%s" with type "%s".', $controller, $parameterName, $parameterType), ControllerException::ERR_INITIALIZATION_FAILED);
                }
            }
        }

        return $params;
    }

}
