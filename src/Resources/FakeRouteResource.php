<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources;

class FakeRouteResource implements RouteResourceInterface
{
    public function get(): array
    {
        return [];
    }
}
