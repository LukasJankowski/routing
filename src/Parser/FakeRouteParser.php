<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Parser;

final class FakeRouteParser implements RouteParserInterface
{
    public function parse(array $routes): array
    {
        return [];
    }
}
