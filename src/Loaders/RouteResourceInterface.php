<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders;

interface RouteResourceInterface
{
    /**
     * Get the routes.
     */
    public function get(): array;
}
