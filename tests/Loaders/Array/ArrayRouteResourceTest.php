<?php

namespace Loaders\Array;

use LukasJankowski\Routing\Loaders\Array\ArrayRouteResource;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class ArrayRouteResourceTest extends TestCase
{
    public function test_it_can_retrieve_the_routes_from_array()
    {
        $resource = new ArrayRouteResource([new Route('get', '/')]);

        $this->assertEquals([new Route('get', '/'),], $resource->get());
    }
}
