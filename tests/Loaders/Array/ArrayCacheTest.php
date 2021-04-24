<?php

namespace Loaders\Array;

use LukasJankowski\Routing\Loaders\Array\ArrayCache;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class ArrayCacheTest extends TestCase
{
    public function test_it_can_store_routes()
    {
        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::match(['post', 'put'], '/route')->build(),
            RouteBuilder::get('/test1')->build(),
            RouteBuilder::get('/test2')->build(),
        ];

        $cache = new ArrayCache();

        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $cache = new ArrayCache();
        $this->assertEquals([], $cache->get());
    }
}
