<?php

namespace Resources\Cache;

use LukasJankowski\Routing\Resources\Cache\ArrayRouteCache;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class ArrayRouteCacheTest extends TestCase
{
    public function test_it_can_store_routes()
    {
        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::match(['post', 'put'], '/route')->build(),
            RouteBuilder::get('/test1')->build(),
            RouteBuilder::get('/test2')->build(),
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
