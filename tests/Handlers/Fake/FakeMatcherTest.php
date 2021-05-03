<?php

namespace Handlers\Fake;

use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class FakeMatcherTest extends TestCase
{
    public function test_it_does_nothing()
    {
        $matcher = new FakeMatcher();

        $this->assertTrue(
            $matcher->matches(
                RouteBuilder::get('/')->build(),
                new Request('get', '', '', '')
            )
        );
    }
}
