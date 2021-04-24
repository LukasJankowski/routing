<?php

namespace Handlers\Fake;

use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Handlers\ParserInterface;
use PHPUnit\Framework\TestCase;

class FakeParserTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $parser = new FakeParser();

        $this->assertInstanceOf(ParserInterface::class, $parser);
        $this->assertInstanceOf(FakeParser::class, $parser);
    }

    public function test_it_does_nothing()
    {
        $parser = new FakeParser();

        $this->assertEquals([], $parser->parse([]));
    }
}
