<?php

namespace Loaders\Fake;

use LukasJankowski\Routing\Loaders\Fake\FakeResource;
use PHPUnit\Framework\TestCase;

class FakeResourceTest extends TestCase
{
    public function test_it_does_nothing()
    {
        $resource = new FakeResource();

        $this->assertEquals([], $resource->get());
    }
}
