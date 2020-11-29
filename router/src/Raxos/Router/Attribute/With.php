<?php
declare(strict_types=1);

namespace Raxos\Router\Attribute;

use Attribute;

/**
 * Class With
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Attribute
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class With
{

    /**
     * With constructor.
     *
     * @param string $class
     * @param array $arguments
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(private string $class, private array $arguments = [])
    {
    }

    /**
     * Gets the middleware arguments.
     *
     * @return array
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public final function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Gets the middleware class.
     *
     * @return string
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public final function getClass(): string
    {
        return $this->class;
    }

}
