<?php

namespace Handlers\Fake;

use LukasJankowski\Routing\Handlers\Fake\FakeRouteMatcher;
use LukasJankowski\Routing\Handlers\RouteMatcherInterface;
use LukasJankowski\Routing\Handlers\Regex\RegexRouteMatcher;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class FakeRouteMatcherTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $matcher = new FakeRouteMatcher();

        $this->assertInstanceOf(RouteMatcherInterface::class, $matcher);
        $this->assertInstanceOf(FakeRouteMatcher::class, $matcher);
    }

    public function test_it_does_nothing()
    {
        $matcher = new FakeRouteMatcher();

        $this->assertTrue(
            $matcher->matches(
                new Route('/'),
                new Request('get', '', '', '')
            )
        );
    }
}
