<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders;

use LukasJankowski\Routing\Route;

interface CacheInterface extends ResourceInterface
{
    /**
     * Save the routes.
     *
     * @param array<Route> $routes
     */
    public function set(array $routes): void;
}
