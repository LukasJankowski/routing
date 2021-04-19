<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources\Cache;

use LukasJankowski\Routing\Resources\RouteResourceInterface;

interface RouteCacheInterface extends RouteResourceInterface
{
    public function set(array $routes): void;
}
