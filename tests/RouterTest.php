<?php

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
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

    public function test_it_constructs_the_route_match()
    {
        $route = RouteBuilder::get('/', ['some', 'action'])
            ->name('name')
            ->host('host.com')
            ->scheme('https')
            ->constraint(['to' => '\d+'])
            ->middleware('test_middleware')
            ->default(['to' => 'default'])
            ->build();

        $route->parsedParameters['param'] = 'value';

        $request = new Request('GET', '/', 'host.com', 'HTTPS');

        $match = Router::makeMatch($route, $request);

        $this->assertEquals('/', $match->getPath());
        $this->assertEquals('/', $match->getRoute());
        $this->assertEquals('name', $match->getName());
        $this->assertEquals(['test_middleware'], $match->getMiddlewares());
        $this->assertEquals(['some', 'action'], $match->getAction());
        $this->assertEquals(['param' => 'value'], $match->getParameters());
    }
}
