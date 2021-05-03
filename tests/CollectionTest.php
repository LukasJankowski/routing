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
    private Collection $collection;

    protected function setUp(): void
    {
        $this->collection = new Collection(new DefaultHandler(new FakeMatcher(), new FakeParser()));
    }

    public function test_it_can_be_instantiated()
    {
        $collection = $this->collection;

        $this->assertEmpty($collection->getRoutes());
        $this->assertEquals('default', $collection->getName());
    }

    public function test_it_can_add_routes()
    {
        $collection = $this->collection;
        $collection->add(RouteBuilder::get('/')->build());
        $collection->add(RouteBuilder::get('/path')->build());

        $collection->addMany([RouteBuilder::get('/test')->build(), RouteBuilder::get('/another')->build()]);

        $this->assertCount(4, $collection->getRoutes());
    }

    public function test_it_has_convenience()
    {
        $collection = $this->collection;
        $collection->add(RouteBuilder::get('/')->build());

        $this->assertIsIterable($collection);
        $this->assertInstanceOf(ArrayIterator::class, $collection->getIterator());

        $this->assertCount(1, $collection);
        $this->assertEquals(1, $collection->count());
    }

    public function test_it_can_load_routes_from_resource()
    {
        $route = [RouteBuilder::get('/')->build()];
        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(null, new ArrayResource($route))
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals($route, $collection->getRoutes());
    }

    public function test_it_can_load_routes_from_cache()
    {
        $route = [RouteBuilder::get('/')->build()];
        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new ArrayCache($route), new ArrayResource())
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals($route, $collection->getRoutes());
    }

    public function test_it_prefers_loading_from_cache()
    {
        $cacheRoute = RouteBuilder::get('/')->build();
        $resourceRoute = RouteBuilder::post('/path')->build();

        $collection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new ArrayCache([$cacheRoute]), new ArrayResource([$resourceRoute]))
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$cacheRoute], $collection->getRoutes());
    }

    public function test_it_can_dynamically_load_from_resource()
    {
        $cacheRoute = RouteBuilder::get('/')->build();
        $resourceRoute = RouteBuilder::post('/path')->build();

        $cache = new ArrayCache([$cacheRoute]);
        $resource = new ArrayResource([$resourceRoute]);

        $collection = $this->collection;
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

        $collection = $this->collection;

        $this->assertFalse($collection->isParsed());

        $collection->add($route);
        $collection->parse();

        $this->assertEquals([], $collection->getRoutes());
        $this->assertTrue($collection->isParsed());
    }

    public function test_it_can_match_routes()
    {
        $route = RouteBuilder::get('/test')->build();

        $collection = $this->collection;

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

        $collection = $this->collection;
        $collection->add($route);

        $this->assertEquals($route, $collection->getRouteByName('route.name'));
        $this->assertNull($collection->getRouteByName('another.name'));
    }
}
