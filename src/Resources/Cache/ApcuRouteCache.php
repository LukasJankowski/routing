<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources\Cache;

use ErrorException;
use RuntimeException;

final class ApcuRouteCache implements RouteCacheInterface
{
    /**
     * ApcuRouteCache constructor.
     */
    public function __construct(private string $key)
    {
        if (! extension_loaded('apcu')) {
            throw new RuntimeException('Extension APCu must be loaded.');
        }
    }

    /**
     * @inheritDoc
     */
    public function set(array $routes): void
    {
        if (! apcu_store($this->key, $routes)) {
            throw new ErrorException('Failed storing routes in APCu cache.');
        }
    }

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        $success = false;
        $routes = apcu_fetch($this->key, $success);

        if (! $success) {
            //throw new ErrorException('Failed fetching routes from APCu cache.');
            return [];
        }

        return $routes;
    }
}
