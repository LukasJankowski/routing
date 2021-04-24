<?php

namespace Loaders;

use LukasJankowski\Routing\Loaders\Array\ArrayCache;
use LukasJankowski\Routing\Loaders\Array\ArrayResource;
use LukasJankowski\Routing\Loaders\DefaultLoader;
use LukasJankowski\Routing\Loaders\Fake\FakeCache;
use LukasJankowski\Routing\Loaders\Fake\FakeResource;
use LukasJankowski\Routing\Loaders\LoaderInterface;
use LukasJankowski\Routing\Loaders\ResourceInterface;
use LukasJankowski\Routing\Loaders\CacheInterface;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class DefaultLoaderTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $loader = new DefaultLoader(new FakeCache(), new FakeResource());

        $this->assertInstanceOf(LoaderInterface::class, $loader);
        $this->assertInstanceOf(ResourceInterface::class, $loader);
        $this->assertInstanceOf(CacheInterface::class, $loader);
    }

    public function test_it_can_fetch_cached_routes()
    {
        $route = new Route('/');

        $cache = new ArrayCache([$route]);

        $loader = new DefaultLoader($cache);

        $this->assertEquals([$route], $loader->get());
    }

    public function test_it_can_fetch_routes_from_resource()
    {
        $route = new Route('/');

        $resource = new ArrayResource([$route]);


        $loader = new DefaultLoader(null, $resource);

        $this->assertEquals([$route], $loader->get());
    }

    public function test_it_can_store_routes_in_cache()
    {
        $route = new Route('/');

        $cache = new ArrayCache();

        $loader = new DefaultLoader($cache, null);

        $loader->set([$route]);

        $this->assertEquals([$route], $cache->get());
    }
}

