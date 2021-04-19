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
    public function __construct(
        protected RouteMatcherInterface $matcher,
        protected ?RouteCacheInterface $cache = null,
        protected string $name = 'default'
    ) {
        if ($this->cache) {
            $this->fromResource($this->cache);
        }
    }

    public function fromResource(RouteResourceInterface $resource): void
    {
        $this->addMany($resource->get());
    }

    public function addMany(array $routes): void
    {
        array_map([$this, 'add'], $routes);
    }

    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function cache(): void
    {
        if ($this->cache !== null) {
            $this->cache->set($this->routes);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }


    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->routes);
    }

    public function count(): int
    {
        return count($this->routes);
    }
}
