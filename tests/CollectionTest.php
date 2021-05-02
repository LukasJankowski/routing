<?php

use LukasJankowski\Routing\Handlers\DefaultHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Loaders\Array\ArrayCache;
use LukasJankowski\Routing\Loaders\Array\ArrayResource;
use LukasJankowski\Routing\Loaders\DefaultLoader;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use LukasJankowski\Routing\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $collection = new Collection(new DefaultHandler(new FakeMatcher(), new FakeParser()));

        $this->assertEmpty($collection->getRoutes());
        $this->assertEquals('default', $collection->getName());
        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function test_it_can_add_routes()
    {
        $collection = new Collection(new DefaultHandler(new FakeMatcher(), new FakeParser()));
        $collection->add(new Route('get', '/'));
        $collection->add(new Route('get', '/path'));

        $collection->addMany([new Route('get', '/test'), new Route('get', '/another')]);

        $this->assertCount(4, $collection->getRoutes());
    }

    public function test_it_has_convenience()
    {
        $collection = new Collection(new DefaultHandler(new FakeMatcher(), new FakeParser()));
        $collection->add(new Route('get', '/'));

        $this->assertIsIterable($collection);
        $this->assertIsIterable($collection->getIterator());

        $this->assertCount(1, $collection);

    }

    public function test_it_can_load_routes_from_resource()
    {
        $route = new Route('get', '/');
        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(null, new ArrayResource([$route]))
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$route], $collection->getRoutes());
    }

    public function test_it_can_load_routes_from_cache()
    {
        $route = new Route('get', '/');
        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new ArrayCache([$route]), new ArrayResource())
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$route], $collection->getRoutes());
    }

    public function test_it_prefers_loading_from_cache()
    {
        $cacheRoute = new Route('get', '/');
        $resourceRoute = new Route('post', '/path');

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new ArrayCache([$cacheRoute]), new ArrayResource([$resourceRoute]))
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$cacheRoute], $collection->getRoutes());
    }

    public function test_it_can_dynamically_load_from_resource()
    {
        $cacheRoute = new Route('get', '/');
        $resourceRoute = new Route('post', '/path');

        $cache = new ArrayCache([$cacheRoute]);
        $resource = new ArrayResource([$resourceRoute]);

        $collection = new Collection(new DefaultHandler(new FakeMatcher(), new FakeParser()));
        $collection->fromLoader(new DefaultLoader($cache));
        $collection->fromLoader(new DefaultLoader(null, $resource));

        $this->assertCount(2, $collection->getRoutes());
        $this->assertEquals([$cacheRoute, $resourceRoute], $collection->getRoutes());
    }

    public function test_it_can_load_from_multiple_sources()
    {
        $cacheRoute = RouteBuilder::get('/')->build();
        $resourceRoute = RouteBuilder::post('/path')->build();
        $addRoute = RouteBuilder::put('/another')->build();
        $manyRoutes = [RouteBuilder::patch('/test')->build(), RouteBuilder::delete('/delete')->build()];

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new ArrayCache([$cacheRoute]))
        );

        $collection->fromLoader(new DefaultLoader(null, new ArrayResource([$resourceRoute])));
        $collection->add($addRoute);
        $collection->addMany($manyRoutes);

        $this->assertCount(5, $collection->getRoutes());
        $this->assertEquals([$cacheRoute, $resourceRoute, $addRoute, ...$manyRoutes], $collection->getRoutes());
    }

    public function test_it_can_cache_its_routes()
    {
        $manyRoutes = [RouteBuilder::patch('/test')->build(), RouteBuilder::delete('/delete')->build()];

        $cache = new ArrayCache();

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader($cache)
        );

        $collection->addMany($manyRoutes);
        $collection->cache();

        $this->assertEquals($manyRoutes, $cache->get());
    }

    public function test_it_can_parse_routes()
    {
        $route = RouteBuilder::patch('/test')->build();

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );

        $collection->add($route);

        $collection->parse();

        $this->assertEquals([], $collection->getRoutes());
    }

    public function test_it_can_match_routes()
    {
        $route = RouteBuilder::get('/test')->build();

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );

        $collection->add($route);

        $reflection = new \ReflectionClass($collection);
        $property = $reflection->getProperty('parsed');
        $property->setAccessible(true);
        $property->setValue($collection, true);

        $this->assertInstanceOf(
            Route::class,
            $collection->match(
                new Request('get', '/test', '', '')
            )
        );

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );

        $this->assertFalse(
            $collection->match(
                new Request('get', '/test', '', '')
            )
        );
    }

    public function test_can_get_route_by_name()
    {
        $route = RouteBuilder::get('/')->name('route.name')->build();

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );
        $collection->add($route);

        $this->assertEquals($route, $collection->getRouteByName('route.name'));
        $this->assertNull($collection->getRouteByName('another.name'));
    }
}
