<?php

namespace Resources\Cache;

use LukasJankowski\Routing\Resources\Cache\PhpRouteCache;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class PhpRouteCacheTest extends TestCase
{
    public function test_it_can_read_and_write_to_file()
    {
        $cache = new PhpRouteCache(__DIR__ . '/../../fixtures/php_route_cache.cache');

        $routes = [
            new Route('get', '/'),
            new Route(['post', 'put'], '/route'),
            new Route('get', '/test1'),
            new Route('get', '/test2')
        ];

        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $cache = new PhpRouteCache(__DIR__ . '/../../fixtures/doesnt_exist.cache');
        $this->assertEquals([], $cache->get());

        $cache = new PhpRouteCache(__DIR__ . '/../../fixtures/php_cache_invalid.cache');
        $this->assertEquals([], $cache->get());
    }
}
