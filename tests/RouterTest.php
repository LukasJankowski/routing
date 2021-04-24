<?php

use LukasJankowski\Routing\Handlers\DefaultRouteHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteParser;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use LukasJankowski\Routing\RouteCollection;
use LukasJankowski\Routing\RouteMatch;
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

        $router = new Router();
        $match = $router->makeMatch($route, $request);

        $this->assertEquals('/', $match->getPath());
        $this->assertEquals('/', $match->getRoute());
        $this->assertEquals('name', $match->getName());
        $this->assertEquals(['test_middleware'], $match->getMiddlewares());
        $this->assertEquals(['some', 'action'], $match->getAction());
        $this->assertEquals(['param' => 'value'], $match->getParameters());
    }

    public function test_it_can_add_collections()
    {
        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser())
        );

        $router = new Router();

        $router->add($collection);
        $router->addMany([$collection, $collection]);

        $this->assertCount(3, $router->getCollections());
    }

    public function test_it_can_match_against_multiple_collections()
    {
        $stub = $this->createMock(RouteCollection::class);
        $stub->method('match')
            ->willReturn(RouteBuilder::get('/')->build());

        $router = new Router();

        $request = new Request('get', '/', '', '');

        $router->add($stub);

        $this->assertInstanceOf(RouteMatch::class, $router->resolve($request));

        $router = new Router();

        $this->assertFalse($router->resolve($request));
    }
}
