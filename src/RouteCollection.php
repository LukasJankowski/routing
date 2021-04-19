<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use LukasJankowski\Routing\Matchers\RouteMatcherInterface;
use LukasJankowski\Routing\Parser\RouteParserInterface;
use LukasJankowski\Routing\Resources\Cache\RouteCacheInterface;
use LukasJankowski\Routing\Resources\RouteResourceInterface;

final class RouteCollection extends AbstractRouteCollection
{
    protected array $routes = [];

    public function __construct(
        RouteMatcherInterface $matcher,
        private RouteParserInterface $parser,
        private ?RouteResourceInterface $resource = null,
        ?RouteCacheInterface $cache = null,
        string $name = 'default'
    ) {
        parent::__construct($matcher, $cache, $name);

        if ($this->routes === [] && $this->resource) {
            $this->fromResource($this->resource);
        }
    }

    public static function make(
        RouteMatcherInterface $matcher,
        RouteParserInterface $parser,
        ?RouteResourceInterface $resource = null,
        ?RouteCacheInterface $cache = null,
        string $name = 'default'
    ): self|CompiledRouteCollection {
        $collection = new self($matcher, $parser, $resource, $cache, $name);

        if (! empty($collection->getRoutes()) && $collection->getRoutes()[0]->parsedPath !== null) {
            return new CompiledRouteCollection($matcher, $collection->getRoutes(), $cache, $name);
        }
    }

    public function parse(): CompiledRouteCollection
    {
        return new CompiledRouteCollection($this->matcher, $this->parser->parse($this->routes), $this->cache);
    }
}
