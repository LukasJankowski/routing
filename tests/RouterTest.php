<?php

use LukasJankowski\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function test_it_builds_patterns()
    {
        $this->assertIsString(Router::specificDynamicSegmentPattern('name'));
        $this->assertIsString(Router::dynamicSegmentPattern());
        $this->assertIsString(Router::openingIdentifier());
        $this->assertIsString(Router::closingIdentifier());
        $this->assertIsString(Router::wildcardIdentifier());
        $this->assertIsString(Router::optionalIdentifier());
        $this->assertIsString(Router::wildcardPattern());
        $this->assertIsString(Router::optionalPattern());
    }
}
