<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders;

final class DefaultLoader implements LoaderInterface
{
    /**
     * DefaultLoader constructor.
     */
    public function __construct(
        private ?CacheInterface $cache = null,
        private ?ResourceInterface $resource = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function set(array $routes): void
    {
        $this->cache->set($routes);
    }

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        $routes = $this->cache ? $this->cache->get() : [];

        return empty($routes) && $this->resource ? $this->resource->get() : $routes;
    }
}
