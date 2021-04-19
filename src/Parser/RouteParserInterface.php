<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Parser;

interface RouteParserInterface
{
    /**
     * Parse the routes for the matcher.
     */
    public function parse(array $routes): array;
}
