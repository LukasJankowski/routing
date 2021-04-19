<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Matchers;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

interface RouteMatcherInterface
{
    public function matches(Route $route, Request $request): bool;
}
