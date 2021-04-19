<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources\Cache;

use ErrorException;

final class PhpRouteCache implements RouteCacheInterface
{
    public function __construct(private string $file)
    {
    }

    public function set(array $routes): void
    {
        if (file_put_contents($this->file, serialize($routes)) === false) {
            throw new ErrorException('Failed storing routes in PHP file cache.');
        }
    }

    public function get(): array
    {
        if (! file_exists($this->file)) {
            return [];
        }

        if (false === $routes = file_get_contents($this->file)) {
            //throw new ErrorException('Failed fetching routes from PHP file cache.');
            return [];
        }

        if (false === $routes = unserialize($routes)) {
            //throw new ErrorException('Failed unserializing routes from PHP file cache.');
            return [];
        }

        return $routes;
    }
}
