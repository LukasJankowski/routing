<?php

namespace Handlers;

use LukasJankowski\Routing\Handlers\DefaultHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Handlers\HandlerInterface;
use LukasJankowski\Routing\Handlers\MatcherInterface;
use LukasJankowski\Routing\Handlers\ParserInterface;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class DefaultHandlerTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $handler = new DefaultHandler(new FakeMatcher(), new FakeParser());

        $this->assertInstanceOf(MatcherInterface::class, $handler);
        $this->assertInstanceOf(ParserInterface::class, $handler);
        $this->assertInstanceOf(HandlerInterface::class, $handler);
    }

    public function test_it_can_parse_routes()
    {
        $route = new Route('/');

        $handler = new DefaultHandler(new FakeMatcher(), new FakeParser());

        $this->assertEquals([], $handler->parse([$route]));
    }

    public function test_it_can_match_routes()
    {
        $route = new Route('/');
        $request = new Request('get', '/', '', '');

        $handler = new DefaultHandler(new FakeMatcher(), new FakeParser());

        $this->assertTrue($handler->matches($route, $request));
    }
}

