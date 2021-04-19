<?php

namespace Resources\Cache;

use LukasJankowski\Routing\Resources\Cache\ArrayRouteCache;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class ArrayRouteCacheTest extends TestCase
{
    public function test_it_can_store_routes()
    {
        $routes = [
            new Route('get', '/'),
            new Route(['post', 'put'], '/route'),
            new Route('get', '/test1'),
            new Route('get', '/test2')
        ];

        $cache = new ArrayRouteCache();

        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $cache = new ArrayRouteCache();
        $this->assertEquals([], $cache->get());
    }
}
