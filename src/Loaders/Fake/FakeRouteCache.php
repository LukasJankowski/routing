<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Fake;

use LukasJankowski\Routing\Loaders\RouteCacheInterface;

final class FakeRouteCache extends FakeRouteResource implements RouteCacheInterface
{
    /**
     * @inheritDoc
     */
    public function set(array $routes): void
    {
    }
}
