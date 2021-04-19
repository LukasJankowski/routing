<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

abstract class AbstractRouteConstraint implements RouteConstraintInterface
{
    protected Route $route;

    protected Request $request;

    public function setRoute(Route $route): void
    {
        $this->route = $route;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
