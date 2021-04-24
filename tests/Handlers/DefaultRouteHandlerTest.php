<?php

namespace Handlers;

use LukasJankowski\Routing\Handlers\DefaultRouteHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteParser;
use LukasJankowski\Routing\Handlers\RouteHandlerInterface;
use LukasJankowski\Routing\Handlers\RouteMatcherInterface;
use LukasJankowski\Routing\Handlers\RouteParserInterface;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class DefaultRouteHandlerTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $handler = new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser());

        $this->assertInstanceOf(RouteMatcherInterface::class, $handler);
        $this->assertInstanceOf(RouteParserInterface::class, $handler);
        $this->assertInstanceOf(RouteHandlerInterface::class, $handler);
    }

    public function test_it_can_parse_routes()
    {
        $route = new Route('/');

        $handler = new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser());

        $this->assertEquals([], $handler->parse([$route]));
    }

    public function test_it_can_match_routes()
    {
        $route = new Route('/');
        $request = new Request('get', '/', '', '');

        $handler = new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser());

        $this->assertTrue($handler->matches($route, $request));
    }
}

