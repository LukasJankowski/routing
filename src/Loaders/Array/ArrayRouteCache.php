<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Array;

use LukasJankowski\Routing\Loaders\RouteCacheInterface;

final class ArrayRouteCache extends ArrayRouteResource implements RouteCacheInterface
{
    /**
     * @inheritDoc
     */
    public function set(array $routes): void
    {
        $this->routes = $routes;
    }
}
