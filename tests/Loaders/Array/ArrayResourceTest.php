<?php

namespace Loaders\Array;

use LukasJankowski\Routing\Loaders\Array\ArrayResource;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class ArrayResourceTest extends TestCase
{
    public function test_it_can_retrieve_the_routes_from_array()
    {
        $route = [RouteBuilder::get('/')->build()];
        $resource = new ArrayResource($route);

        $this->assertEquals($route, $resource->get());
    }
}
