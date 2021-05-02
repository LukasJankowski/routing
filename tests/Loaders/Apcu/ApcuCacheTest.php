<?php

namespace Loaders\Apcu;

use LukasJankowski\Routing\Loaders\Apcu\ApcuCache;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ApcuCacheTest extends TestCase
{
    public function test_it_throws_an_exception_if_extension_is_not_loaded()
    {
        if (! extension_loaded('apcu')) {
            $this->expectException(RuntimeException::class);
            new ApcuCache('key');
        } else {
            $this->markTestSkipped();
        }
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        if (! extension_loaded('apcu')) {
            $this->markTestSkipped();
        }

        $cache = new ApcuCache('not-existent');
        $this->assertEquals([], $cache->get());
    }

    public function test_it_can_read_and_write_routes()
    {
        if (! extension_loaded('apcu')) {
            $this->markTestSkipped();
        }

        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::match(['post', 'put'], '/route')->build(),
            RouteBuilder::get('/test1')->build(),
            RouteBuilder::get('/test2')->build(),
        ];

        $cache = new ApcuCache('existing');
        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }
}
