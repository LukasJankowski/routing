<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers\Fake;

use LukasJankowski\Routing\Handlers\MatcherInterface;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

final class FakeMatcher implements MatcherInterface
{
    /**
     * @inheritDoc
     */
    public function matches(Route $route, Request $request, bool $return = true): bool
    {
        return $return;
    }
}
