<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers;

use LukasJankowski\Routing\Route;

interface ParserInterface
{
    /**
     * Parse the routes for the matcher.
     *
     * @param array<Route> $routes
     */
    public function parse(array $routes): array;
}
