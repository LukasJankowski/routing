<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources;

class ArrayRouteResource implements RouteResourceInterface
{
    public function __construct(protected array $routes = [])
    {
    }

    public function get(): array
    {
        return $this->routes;
    }
}
