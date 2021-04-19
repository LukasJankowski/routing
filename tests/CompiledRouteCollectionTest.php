<?php

use LukasJankowski\Routing\CompiledRouteCollection;
use LukasJankowski\Routing\Matchers\FakeRouteMatcher;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Resources\Cache\ArrayRouteCache;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class CompiledRouteCollectionTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $collection = new CompiledRouteCollection(new FakeRouteMatcher());

        $this->assertEmpty($collection->getRoutes());
        $this->assertEquals('default', $collection->getName());
        $this->assertInstanceOf(CompiledRouteCollection::class, $collection);
    }

    public function test_it_can_add_routes()
    {
        $collection = new CompiledRouteCollection(new FakeRouteMatcher());
        $collection->add(new Route('get', '/'));
        $collection->add(new Route('get', '/path'));

        $collection->addMany([new Route('get', '/test'), new Route('get', '/another')]);

        $this->assertCount(4, $collection->getRoutes());
    }

    public function test_it_has_convenience()
    {
        $collection = new CompiledRouteCollection(new FakeRouteMatcher());
        $collection->add(new Route('get', '/'));

        $this->assertIsIterable($collection);
        $this->assertCount(1, $collection);
    }

    public function test_it_can_load_routes_from_cache()
    {
        $route = new Route('get', '/');
        $collection = new CompiledRouteCollection(new FakeRouteMatcher(), cache: new ArrayRouteCache([$route]));

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$route], $collection->getRoutes());
    }

    public function test_it_can_dynamically_load_from_resource()
    {
        $cacheRoute = new Route('get', '/');
        $constructorRoute = new Route('post', '/construct');

        $cache = new ArrayRouteCache([$cacheRoute]);

        $collection = new CompiledRouteCollection(new FakeRouteMatcher(), [$constructorRoute]);
        $collection->fromResource($cache);

        $this->assertCount(2, $collection->getRoutes());
        $this->assertEquals([$constructorRoute, $cacheRoute], $collection->getRoutes());
    }

    public function test_it_can_load_from_multiple_sources()
    {
        $cacheRoute = new Route('get', '/');
        $addRoute = new Route('put', '/another');
        $manyRoutes = [new Route('patch', '/test'), new Route('delete', '/delete')];

        $collection = new CompiledRouteCollection(new FakeRouteMatcher(), cache: new ArrayRouteCache([$cacheRoute]));

        $collection->add($addRoute);
        $collection->addMany($manyRoutes);

        $this->assertCount(4, $collection->getRoutes());
        $this->assertEquals([$cacheRoute, $addRoute, ...$manyRoutes], $collection->getRoutes());
    }

    public function test_it_can_cache_its_routes()
    {
        $manyRoutes = [new Route('patch', '/test'), new Route('delete', '/delete')];

        $cache = new ArrayRouteCache();

        $collection = new CompiledRouteCollection(new FakeRouteMatcher(), cache: $cache);

        $collection->addMany($manyRoutes);
        $collection->cache();

        $this->assertEquals($manyRoutes, $cache->get());
    }

    public function test_it_throws_an_exception_when_trying_to_match_unparsed_routes()
    {
        $manyRoutes = [new Route('patch', '/test'), new Route('delete', '/delete')];

        $collection = new CompiledRouteCollection(new FakeRouteMatcher(), $manyRoutes);

        $this->expectException(RuntimeException::class);

        $collection->match(new Request('get', '/', '', ''));
    }

    public function test_it_can_match_routes()
    {
        $route = new Route('patch', '/test');
        $route->parsedPath = true;

        $collection = new CompiledRouteCollection(new FakeRouteMatcher(), [$route, $route]);
        $this->assertTrue($collection->match(new Request('get', '/test', '', '')));

        $collection = new CompiledRouteCollection(new FakeRouteMatcher(),);
        $this->assertFalse($collection->match(new Request('get', '/test', '', '')));
    }
}
