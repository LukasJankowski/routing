<?php

namespace Loaders\Array;

use LukasJankowski\Routing\Loaders\Array\ArrayResource;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class ArrayResourceTest extends TestCase
{
    public function test_it_can_retrieve_the_routes_from_array()
    {
        $resource = new ArrayResource([new Route('get', '/')]);

        $this->assertEquals([new Route('get', '/'),], $resource->get());
    }
}
