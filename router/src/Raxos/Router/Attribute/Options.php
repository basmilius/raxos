<?php
declare(strict_types=1);

namespace Raxos\Router\Attribute;

use Attribute;
use Raxos\Http\HttpMethods;

/**
 * Class Options
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Attribute
 * @since 2.0.0
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Options extends Route
{

    /**
     * Options constructor.
     *
     * @param string $path
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(string $path)
    {
        parent::__construct($path, HttpMethods::OPTIONS);
    }

}
