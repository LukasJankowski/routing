<?php

namespace Loaders\Redis;

use LukasJankowski\Routing\Loaders\Redis\RedisCache;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class RedisCacheTest extends TestCase
{
    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $cache = new RedisCache(new Client(),'not-existent');
        $this->assertEquals([], $cache->get());
    }

    public function test_it_can_read_and_write_routes()
    {
        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::match(['post', 'put'], '/route')->build(),
            RouteBuilder::get('/test1')->build(),
            RouteBuilder::get('/test2')->constraint('var', 'value')->build(),
        ];

        $cache = new RedisCache(new Client(), 'route-key');
        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }
}
