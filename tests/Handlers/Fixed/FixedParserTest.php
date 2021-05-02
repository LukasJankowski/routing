<?php

namespace Handlers\Fixed;

use LukasJankowski\Routing\Handlers\Fixed\FixedParser;
use LukasJankowski\Routing\Handlers\ParserInterface;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class FixedParserTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $parser = new FixedParser();

        $this->assertInstanceOf(FixedParser::class, $parser);
        $this->assertInstanceOf(ParserInterface::class, $parser);
    }

    public function test_it_can_parse_routes()
    {
        $parser = new FixedParser();

        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::get('/path')->build(),
            RouteBuilder::get('/deeply/nested')->build(),
        ];

        $this->assertEquals($routes, $parser->parse($routes));
    }
}
