<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources\Cache;

use LukasJankowski\Routing\Resources\ArrayRouteResource;

final class ArrayRouteCache extends ArrayRouteResource implements RouteCacheInterface
{
    public function set(array $routes): void
    {
        $this->routes = $routes;
    }
}
