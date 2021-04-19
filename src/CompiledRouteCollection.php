<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use LukasJankowski\Routing\Matchers\RouteMatcherInterface;
use LukasJankowski\Routing\Resources\Cache\RouteCacheInterface;
use RuntimeException;

final class CompiledRouteCollection extends AbstractRouteCollection
{
    /**
     * CompiledRouteCollection constructor.
     */
    public function __construct(
        RouteMatcherInterface $matcher,
        protected array $routes = [],
        ?RouteCacheInterface $cache = null,
        string $name = 'default'
    ) {
        parent::__construct($matcher, $cache, $name);
    }

    /**
     * Match the routes.
     */
    public function match(Request $request): bool
    {
        foreach ($this->routes as $route) {
            if ($route->parsedPath === null) {
                throw new RuntimeException('Compiled route collections require parsed routes.');
            }

            if ($this->matcher->matches($route, $request)) {
                return true;
            }
        }

        return false;
    }
}
