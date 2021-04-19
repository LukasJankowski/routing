<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use LukasJankowski\Routing\Matchers\RouteMatcherInterface;
use LukasJankowski\Routing\Resources\Cache\RouteCacheInterface;
use LukasJankowski\Routing\Resources\RouteResourceInterface;

abstract class AbstractRouteCollection implements IteratorAggregate, Countable
{
    /**
     * AbstractRouteCollection constructor.
     */
    public function __construct(
        protected RouteMatcherInterface $matcher,
        protected ?RouteCacheInterface $cache = null,
        protected string $name = 'default'
    ) {
        if ($this->cache) {
            $this->fromResource($this->cache);
        }
    }

    /**
     * Fetch routes from resource.
     */
    public function fromResource(RouteResourceInterface $resource): void
    {
        $this->addMany($resource->get());
    }

    /**
     * Add many routes.
     */
    public function addMany(array $routes): void
    {
        array_map([$this, 'add'], $routes);
    }

    /**
     * Add a route.
     */
    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Cache the routes.
     */
    public function cache(): void
    {
        if ($this->cache !== null) {
            $this->cache->set($this->routes);
        }
    }

    /**
     * Getter.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Getter.
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->routes);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->routes);
    }
}
