<?php

namespace Loaders\Php;

use LukasJankowski\Routing\Loaders\Php\PhpCache;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class PhpCacheTest extends TestCase
{
    public function test_it_can_read_and_write_to_file()
    {
        $cache = new PhpCache(__DIR__ . '/../../fixtures/php_route_cache.cache');

        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::match(['post', 'put'], '/route')->build(),
            RouteBuilder::get('/test1')->build(),
            RouteBuilder::get('/test2')->build(),
        ];

        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $cache = new PhpCache(__DIR__ . '/../../fixtures/doesnt_exist.cache');
        $this->assertEquals([], $cache->get());

        $cache = new PhpCache(__DIR__ . '/../../fixtures/php_cache_invalid.cache');
        $this->assertEquals([], $cache->get());
    }
}
