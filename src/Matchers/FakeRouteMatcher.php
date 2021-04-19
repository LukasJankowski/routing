<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Matchers;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

final class FakeRouteMatcher implements RouteMatcherInterface
{
    public function matches(Route $route, Request $request, bool $return = true): bool
    {
        return $return;
    }
}
