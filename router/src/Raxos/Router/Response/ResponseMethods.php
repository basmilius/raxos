<?php
declare(strict_types=1);

namespace Raxos\Router\Response;

/**
 * Trait ResponseMethods
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Controller
 * @since 2.0.0
 */
trait ResponseMethods
{

    private static array $headers = [];

    /**
     * Adds the given header to the response.
     *
     * @param string $name
     * @param string $value
     * @param bool $replace
     *
     * @return $this
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected function header(string $name, string $value, bool $replace = true): static
    {
        if (array_key_exists($name, static::$headers) && !$replace) {
            if (is_array(static::$headers[$name])) {
                static::$headers[$name][] = $value;
            } else {
                static::$headers[$name] = [static::$headers[$name], $value];
            }
        } else {
            static::$headers[$name] = $value;
        }

        return $this;
    }

    /**
     * Returns a JSON response.
     *
     * @param mixed $value
     *
     * @return JsonResponse
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected final function json(mixed $value): JsonResponse
    {
        return new JsonResponse($this->router, [], $value);
    }

}
