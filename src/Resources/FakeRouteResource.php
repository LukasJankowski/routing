<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources;

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
