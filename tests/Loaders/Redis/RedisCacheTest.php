<?php

namespace Loaders\Redis;

use LukasJankowski\Routing\Loaders\Redis\RedisCache;
use LukasJankowski\Routing\RouteBuilder;
use Mockery;
use PHPUnit\Framework\TestCase;

class RedisCacheTest extends TestCase
{
    private Mockery\MockInterface $mock;

    protected function setUp(): void
    {
        $this->mock = Mockery::mock('Predis\Client');
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $this->mock->shouldReceive('get')
            ->andReturn(null);

        $cache = new RedisCache($this->mock, 'not-existent');
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

        $this->mock->shouldReceive('set')
            ->andReturn(null);
        $this->mock->shouldReceive('get')
            ->andReturn(serialize($routes));

        $cache = new RedisCache($this->mock, 'route-key');
        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }
}
