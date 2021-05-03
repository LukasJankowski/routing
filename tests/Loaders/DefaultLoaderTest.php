<?php

namespace Loaders;

use LukasJankowski\Routing\Loaders\Array\ArrayCache;
use LukasJankowski\Routing\Loaders\Array\ArrayResource;
use LukasJankowski\Routing\Loaders\DefaultLoader;
use LukasJankowski\Routing\Loaders\Php\PhpCache;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class DefaultLoaderTest extends TestCase
{
    public function test_it_can_fetch_cached_routes()
    {
        $route = [RouteBuilder::get('/')->build()];

        $cache = new ArrayCache($route);
        $loader = new DefaultLoader($cache);

        $this->assertEquals($route, $loader->get());
    }

    public function test_it_can_fetch_routes_from_resource()
    {
        $route = [RouteBuilder::get('/')->build()];

        $resource = new ArrayResource($route);
        $loader = new DefaultLoader(null, $resource);

        $this->assertEquals($route, $loader->get());
    }

    public function test_it_can_store_routes_in_cache()
    {
        $route = [RouteBuilder::get('/')->build()];

        $cache = new ArrayCache();
        $loader = new DefaultLoader($cache, null);
        $loader->set($route);

        $this->assertEquals($route, $cache->get());
    }

    public function test_it_throws_an_exception_when_trying_to_store_a_closure_in_cache()
    {
        $route = [RouteBuilder::get('/', function () {
        })->build()];

        $cache = new PhpCache(__DIR__ . '/../fixtures/php_cache_invalid.cache');

        $this->expectExceptionMessage("Serialization of 'Closure' is not allowed");

        $cache->set($route);
    }
}
