<?php

namespace Loaders;

use LukasJankowski\Routing\Loaders\Array\ArrayRouteCache;
use LukasJankowski\Routing\Loaders\Array\ArrayRouteResource;
use LukasJankowski\Routing\Loaders\DefaultRouteLoader;
use LukasJankowski\Routing\Loaders\Fake\FakeRouteCache;
use LukasJankowski\Routing\Loaders\Fake\FakeRouteResource;
use LukasJankowski\Routing\Loaders\RouteLoaderInterface;
use LukasJankowski\Routing\Loaders\RouteResourceInterface;
use LukasJankowski\Routing\Loaders\RouteCacheInterface;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class DefaultRouteLoaderTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $loader = new DefaultRouteLoader(new FakeRouteCache(), new FakeRouteResource());

        $this->assertInstanceOf(RouteLoaderInterface::class, $loader);
        $this->assertInstanceOf(RouteResourceInterface::class, $loader);
        $this->assertInstanceOf(RouteCacheInterface::class, $loader);
    }

    public function test_it_can_fetch_cached_routes()
    {
        $route = new Route('/');

        $cache = new ArrayRouteCache([$route]);

        $loader = new DefaultRouteLoader($cache);

        $this->assertEquals([$route], $loader->get());
    }

    public function test_it_can_fetch_routes_from_resource()
    {
        $route = new Route('/');

        $resource = new ArrayRouteResource([$route]);


        $loader = new DefaultRouteLoader(null, $resource);

        $this->assertEquals([$route], $loader->get());
    }

    public function test_it_can_store_routes_in_cache()
    {
        $route = new Route('/');

        $cache = new ArrayRouteCache();

        $loader = new DefaultRouteLoader($cache, null);

        $loader->set([$route]);

        $this->assertEquals([$route], $cache->get());
    }
}

