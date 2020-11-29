<?php
declare(strict_types=1);

namespace Raxos\Router\Error;

/**
 * Class RegisterException
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Error
 * @since 2.0.0
 */
final class RegisterException extends RouterException
{

    public const ERR_MAPPING_FAILED = 1;
    public const ERR_MISSING_TYPE = 2;
    public const ERR_ILLEGAL_TYPE = 4;
    public const ERR_NOT_A_CONTROLLER = 8;
    public const ERR_RECURSION_DETECTED = 16;

}
