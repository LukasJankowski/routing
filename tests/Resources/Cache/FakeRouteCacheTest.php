<?php

namespace Resources\Cache;

use LukasJankowski\Routing\Resources\Cache\ArrayRouteCache;
use LukasJankowski\Routing\Resources\Cache\FakeRouteCache;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class FakeRouteCacheTest extends TestCase
{
    public function test_it_does_nothing()
    {
        $cache = new FakeRouteCache();

        $this->assertEquals([], $cache->get());

        $cache->set([]);
    }
}
