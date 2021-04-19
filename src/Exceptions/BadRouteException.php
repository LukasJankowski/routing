<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Exceptions;

use Exception;

final class BadRouteException extends Exception
{
    /**
     * BadRouteException constructor.
     */
    public function __construct(string $message = 'routing.failed', $code = 400)
    {
        parent::__construct($message, $code);
    }
}
