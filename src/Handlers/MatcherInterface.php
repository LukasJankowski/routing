<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

interface MatcherInterface
{
    /**
     * Match the route against the request.
     */
    public function matches(Route $route, Request $request): bool;
}
