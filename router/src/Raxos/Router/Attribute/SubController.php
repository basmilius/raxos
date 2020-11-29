<?php
declare(strict_types=1);

namespace Raxos\Router\Attribute;

use Attribute;
use Raxos\Router\Controller\Controller;
use Raxos\Router\Error\RegisterException;

/**
 * Class SubController
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Attribute
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_METHOD)]
class SubController
{

    /**
     * SubController constructor.
     *
     * @param string $class
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(private string $class)
    {
        if (!is_subclass_of($class, Controller::class)) {
            throw new RegisterException(sprintf('Controller class must extend %s.', Controller::class), RegisterException::ERR_NOT_A_CONTROLLER);
        }
    }

    /**
     * Gets the controller class name.
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
