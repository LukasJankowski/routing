<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources\Cache;

use LukasJankowski\Routing\Resources\FakeRouteResource;

final class FakeRouteCache extends FakeRouteResource implements RouteCacheInterface
{
    /**
     * @inheritDoc
     */
    public function set(array $routes): void
    {
    }
}
