<?php
declare(strict_types=1);

namespace Raxos\Router\Error;

/**
 * Class ControllerException
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Error
 * @since 2.0.0
 */
final class ControllerException extends RouterException
{

    public const ERR_INSTANCE_NOT_FOUND = 1;
    public const ERR_INITIALIZATION_FAILED = 2;

}
