<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Array;

use LukasJankowski\Routing\Loaders\ResourceInterface;
use LukasJankowski\Routing\Route;

class ArrayResource implements ResourceInterface
{
    /**
     * ArrayResource constructor.
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
