<?php

namespace Handlers\Fake;

use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\MatcherInterface;
use LukasJankowski\Routing\Handlers\Regex\RegexMatcher;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class FakeMatcherTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $matcher = new FakeMatcher();

        $this->assertInstanceOf(MatcherInterface::class, $matcher);
        $this->assertInstanceOf(FakeMatcher::class, $matcher);
    }

    public function test_it_does_nothing()
    {
        $matcher = new FakeMatcher();

        $this->assertTrue(
            $matcher->matches(
                new Route('/'),
                new Request('get', '', '', '')
            )
        );
    }
}
