<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Apcu;

use ErrorException;
use LukasJankowski\Routing\Loaders\CacheInterface;
use RuntimeException;

final class ApcuCache implements CacheInterface
{
    /**
     * ApcuCache constructor.
     */
    public function __construct(private string $key)
    {
        // apc.enabled = 1
        // apc.enable_cli = 1 // for testing purposes
        if (! extension_loaded('apcu') || ! apcu_enabled()) {
            throw new RuntimeException('Extension APCu must be loaded/enabled.');
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
