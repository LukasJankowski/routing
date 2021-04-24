<?php

namespace Loaders\Fake;

use LukasJankowski\Routing\Loaders\Fake\FakeRouteResource;
use PHPUnit\Framework\TestCase;

class FakeRouteResourceTest extends TestCase
{
    public function test_it_does_nothing()
    {
        $resource = new FakeRouteResource();

        $this->assertEquals([], $resource->get());
    }
}
