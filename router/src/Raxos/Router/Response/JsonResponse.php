<?php
declare(strict_types=1);

namespace Raxos\Router\Response;

use function array_key_exists;

/**
 * Class JsonResponse
 *
 * @author Bas Milius <bas@glybe.nl>
 * @package Raxos\Router\Response
 * @since 2.0.0
 */
class JsonResponse extends Response
{

    public const FLAGS = JSON_BIGINT_AS_STRING | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG;

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected function respondBody(): void
    {
        echo json_encode($this->value, self::FLAGS);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@glybe.nl>
     * @since 2.0.0
     */
    protected function respondHeaders(): void
    {
        if (!array_key_exists('Content-Type', $this->headers)) {
            $this->headers['Content-Type'] = 'application/json';
        }

        parent::respondHeaders();
    }

}
