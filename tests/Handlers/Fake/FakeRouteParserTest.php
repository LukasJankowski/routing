<?php

namespace Handlers\Fake;

use LukasJankowski\Routing\Handlers\Fake\FakeRouteParser;
use LukasJankowski\Routing\Handlers\RouteParserInterface;
use PHPUnit\Framework\TestCase;

class FakeRouteParserTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $parser = new FakeRouteParser();

        $this->assertInstanceOf(RouteParserInterface::class, $parser);
        $this->assertInstanceOf(FakeRouteParser::class, $parser);
    }

    public function test_it_does_nothing()
    {
        $parser = new FakeRouteParser();

        $this->assertEquals([], $parser->parse([]));
    }
}
