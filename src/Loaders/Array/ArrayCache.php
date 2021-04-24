<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Array;

use LukasJankowski\Routing\Loaders\CacheInterface;

final class ArrayCache extends ArrayResource implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function set(array $routes): void
    {
        $this->routes = $routes;
    }
}
