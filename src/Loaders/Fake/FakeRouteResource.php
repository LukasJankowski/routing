<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Fake;

use LukasJankowski\Routing\Loaders\RouteResourceInterface;

class FakeRouteResource implements RouteResourceInterface
{
    /**
     * @inheritDoc
     */
    public function get(): array
    {
        return [];
    }
}
