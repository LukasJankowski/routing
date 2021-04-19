<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources;

class ArrayRouteResource implements RouteResourceInterface
{
    /**
     * ArrayRouteResource constructor.
     */
    public function __construct(protected array $routes = [])
    {
    }

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        return $this->routes;
    }
}
