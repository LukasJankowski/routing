<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers\Fake;

use LukasJankowski\Routing\Handlers\RouteParserInterface;

final class FakeRouteParser implements RouteParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(array $routes): array
    {
        return [];
    }
}
