<?php

namespace Loaders\Fake;

use LukasJankowski\Routing\Loaders\Fake\FakeCache;
use PHPUnit\Framework\TestCase;

class FakeCacheTest extends TestCase
{
    public function test_it_does_nothing()
    {
        $cache = new FakeCache();

        $this->assertEquals([], $cache->get());

        $cache->set([]);
    }
}
