<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Array;

use LukasJankowski\Routing\Loaders\RouteResourceInterface;
use LukasJankowski\Routing\Route;

class ArrayRouteResource implements RouteResourceInterface
{
    /**
     * ArrayRouteResource constructor.
     *
     * @param array<Route> $routes
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
