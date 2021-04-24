<?php

use LukasJankowski\Routing\CompiledRouteCollection;
use LukasJankowski\Routing\Matchers\FakeRouteMatcher;
use LukasJankowski\Routing\Parser\FakeRouteParser;
use LukasJankowski\Routing\Resources\ArrayRouteResource;
use LukasJankowski\Routing\Resources\Cache\ArrayRouteCache;
use LukasJankowski\Routing\Resources\FakeRouteResource;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;

class RouteCollectionTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $collection = new RouteCollection(new FakeRouteMatcher(), new FakeRouteParser());

        $this->assertEmpty($collection->getRoutes());
        $this->assertEquals('default', $collection->getName());
        $this->assertInstanceOf(RouteCollection::class, $collection);
    }

    public function test_it_can_add_routes()
    {
        $collection = new RouteCollection(new FakeRouteMatcher(), new FakeRouteParser());
        $collection->add(new Route('get', '/'));
        $collection->add(new Route('get', '/path'));

        $collection->addMany([new Route('get', '/test'), new Route('get', '/another')]);

        $this->assertCount(4, $collection->getRoutes());
    }

    public function test_it_has_convenience()
    {
        $collection = new RouteCollection(new FakeRouteMatcher(), new FakeRouteParser());
        $collection->add(new Route('get', '/'));

        $this->assertIsIterable($collection);
        $this->assertIsIterable($collection->getIterator());

        $this->assertCount(1, $collection);

    }

    public function test_it_returns_a_compiled_collection_when_parsing()
    {
        $collection = new RouteCollection(new FakeRouteMatcher(), new FakeRouteParser());
        $collection->add(new Route('get', '/'));

        $this->assertInstanceOf(CompiledRouteCollection::class, $collection->parse());
    }

    public function test_it_can_load_routes_from_resource()
    {
        $route = new Route('get', '/');
        $collection = new RouteCollection(
            new FakeRouteMatcher(),
            new FakeRouteParser(),
            new ArrayRouteResource([$route])
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$route], $collection->getRoutes());
    }

    public function test_it_can_load_routes_from_cache()
    {
        $route = new Route('get', '/');
        $collection = new RouteCollection(
            new FakeRouteMatcher(),
            new FakeRouteParser(),
            new FakeRouteResource(),
            new ArrayRouteCache([$route])
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$route], $collection->getRoutes());
    }

    public function test_it_prefers_loading_from_cache()
    {
        $cacheRoute = new Route('get', '/');
        $resourceRoute = new Route('post', '/path');

        $collection = new RouteCollection(
            new FakeRouteMatcher(),
            new FakeRouteParser(),
            new ArrayRouteResource([$resourceRoute]),
            new ArrayRouteCache([$cacheRoute])
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$cacheRoute], $collection->getRoutes());
    }

    public function test_it_can_dynamically_load_from_resource()
    {
        $cacheRoute = new Route('get', '/');
        $resourceRoute = new Route('post', '/path');

        $cache = new ArrayRouteCache([$cacheRoute]);
        $resource = new ArrayRouteResource([$resourceRoute]);

        $collection = new RouteCollection(new FakeRouteMatcher(), new FakeRouteParser());
        $collection->fromResource($cache);
        $collection->fromResource($resource);

        $this->assertCount(2, $collection->getRoutes());
        $this->assertEquals([$cacheRoute, $resourceRoute], $collection->getRoutes());
    }

    public function test_it_can_load_from_multiple_sources()
    {
        $cacheRoute = new Route('get', '/');
        $resourceRoute = new Route('post', '/path');
        $addRoute = new Route('put', '/another');
        $manyRoutes = [new Route('patch', '/test'), new Route('delete', '/delete')];

        $collection = new RouteCollection(
            new FakeRouteMatcher(),
            new FakeRouteParser(),
            cache: new ArrayRouteCache([$cacheRoute])
        );

        $collection->fromResource(new ArrayRouteResource([$resourceRoute]));
        $collection->add($addRoute);
        $collection->addMany($manyRoutes);

        $this->assertCount(5, $collection->getRoutes());
        $this->assertEquals([$cacheRoute, $resourceRoute, $addRoute, ...$manyRoutes], $collection->getRoutes());
    }

    public function test_it_can_cache_its_routes()
    {
        $manyRoutes = [new Route('patch', '/test'), new Route('delete', '/delete')];

        $cache = new ArrayRouteCache();

        $collection = new RouteCollection(
            new FakeRouteMatcher(),
            new FakeRouteParser(),
            cache: $cache
        );

        $collection->addMany($manyRoutes);
        $collection->cache();

        $this->assertEquals($manyRoutes, $cache->get());
    }

    public function test_it_returns_the_matching_collection()
    {
        $route = new Route('put', '/another');
        $route->parsedPath = true;

        $cache = new ArrayRouteCache([$route]);

        $collection = RouteCollection::make(
            new FakeRouteMatcher(),
            new FakeRouteParser(),
            cache: $cache
        );

        $this->assertInstanceOf(CompiledRouteCollection::class, $collection);

        $collection = RouteCollection::make(
            new FakeRouteMatcher(),
            new FakeRouteParser(),
        );

        $this->assertInstanceOf(RouteCollection::class, $collection);
    }
}
