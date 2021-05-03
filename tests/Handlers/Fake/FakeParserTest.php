<?php

namespace Handlers\Fake;

use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use PHPUnit\Framework\TestCase;

class FakeParserTest extends TestCase
{
    public function test_it_does_nothing()
    {
        $parser = new FakeParser();

        $this->assertEquals([], $parser->parse([]));
    }
}
