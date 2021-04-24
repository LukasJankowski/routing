<?php

namespace Loaders\Fake;

use LukasJankowski\Routing\Loaders\Fake\FakeRouteCache;
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
