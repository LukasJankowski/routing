<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers\Fixed;

use LukasJankowski\Routing\Exceptions\BadRouteException;
use LukasJankowski\Routing\Handlers\AbstractMatcher;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

final class FixedMatcher extends AbstractMatcher
{
    /**
     * @inheritDoc
     *
     * @throws BadRouteException
     */
    public function matches(Route $route, Request $request): bool
    {
        if ($request->path !== $route->getPath()) {
            return false;
        }

        $this->matchConstraints($route, $request);

        return true;
    }
}
