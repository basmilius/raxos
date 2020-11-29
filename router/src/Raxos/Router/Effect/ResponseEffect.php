<?php
declare(strict_types=1);

namespace Raxos\Router\Effect;

use Raxos\Router\Response\Response;
use Raxos\Router\Router;

/**
 * Class ResponseEffect
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Effect
 * @since 2.0.0
 */
final class ResponseEffect extends Effect
{

    /**
     * ResponseEffect constructor.
     *
     * @param Router $router
     * @param Response $response
     *
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public function __construct(Router $router, private Response $response)
    {
        parent::__construct($router);
    }

    /**
     * Gets the response.
     *
     * @return Response
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    public final function getResponse(): Response
    {
        return $this->response;
    }

}
