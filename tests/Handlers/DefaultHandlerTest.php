<?php

namespace Handlers;

use LukasJankowski\Routing\Handlers\DefaultHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class DefaultHandlerTest extends TestCase
{
    public function test_it_can_parse_routes()
    {
        $route = [RouteBuilder::get('/')->build()];

        $handler = new DefaultHandler(new FakeMatcher(), new FakeParser());

        $this->assertEquals([], $handler->parse($route));
    }

    public function test_it_can_match_routes()
    {
        $route = RouteBuilder::get('/')->build();
        $request = new Request('get', '/', '', '');

        $handler = new DefaultHandler(new FakeMatcher(), new FakeParser());

        $this->assertTrue($handler->matches($route, $request));
    }
}

