<?php

use LukasJankowski\Routing\Collection;
use LukasJankowski\Routing\Handlers\DefaultHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
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

        $route->setParameters('param', 'value');

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
        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );

        $router = new Router();

        $router->add($collection);
        $router->addMany([$collection, $collection]);

        $this->assertCount(3, $router->getCollections());
    }

    public function test_it_can_match_against_multiple_collections()
    {
        $stub = $this->createMock(Collection::class);
        $stub->method('match')
            ->willReturn(RouteBuilder::get('/')->build());

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );

        $router = new Router();
        $request = new Request('get', '/', '', '');
        $router->addMany([$collection, $stub]);

        $this->assertInstanceOf(RouteMatch::class, $router->resolve($request));

        $router = new Router();

        $this->assertNull($router->resolve($request));
    }

    public function test_can_get_route_by_name()
    {
        $route = RouteBuilder::get('/')->name('route.name')->build();

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );
        $collection->add($route);

        $router = new Router();
        $router->add($collection);

        $this->assertEquals($route, $router->getRouteByName('route.name'));
        $this->assertNull($router->getRouteByName('another.name'));
    }
}
