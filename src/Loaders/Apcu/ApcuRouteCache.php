<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Apcu;

use ErrorException;
use LukasJankowski\Routing\Loaders\RouteCacheInterface;
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
     *
     * @throws ErrorException
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

        return $success ? $routes : [];
    }
}
