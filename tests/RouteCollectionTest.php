<?php

use LukasJankowski\Routing\Handlers\DefaultRouteHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteParser;
use LukasJankowski\Routing\Loaders\Array\ArrayRouteCache;
use LukasJankowski\Routing\Loaders\Array\ArrayRouteResource;
use LukasJankowski\Routing\Loaders\DefaultRouteLoader;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use LukasJankowski\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;

class RouteCollectionTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $collection = new RouteCollection(new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()));

        $this->assertEmpty($collection->getRoutes());
        $this->assertEquals('default', $collection->getName());
        $this->assertInstanceOf(RouteCollection::class, $collection);
    }

    public function test_it_can_add_routes()
    {
        $collection = new RouteCollection(new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()));
        $collection->add(new Route('get', '/'));
        $collection->add(new Route('get', '/path'));

        $collection->addMany([new Route('get', '/test'), new Route('get', '/another')]);

        $this->assertCount(4, $collection->getRoutes());
    }

    public function test_it_has_convenience()
    {
        $collection = new RouteCollection(new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()));
        $collection->add(new Route('get', '/'));

        $this->assertIsIterable($collection);
        $this->assertIsIterable($collection->getIterator());

        $this->assertCount(1, $collection);

    }

    public function test_it_can_load_routes_from_resource()
    {
        $route = new Route('get', '/');
        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(null, new ArrayRouteResource([$route]))
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$route], $collection->getRoutes());
    }

    public function test_it_can_load_routes_from_cache()
    {
        $route = new Route('get', '/');
        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(new ArrayRouteCache([$route]), new ArrayRouteResource())
        );

        $this->assertCount(1, $collection->getRoutes());
        $this->assertEquals([$route], $collection->getRoutes());
    }

    public function test_it_prefers_loading_from_cache()
    {
        $cacheRoute = new Route('get', '/');
        $resourceRoute = new Route('post', '/path');

        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(new ArrayRouteCache([$cacheRoute]), new ArrayRouteResource([$resourceRoute]))
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

        $collection = new RouteCollection(new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()));
        $collection->fromLoader(new DefaultRouteLoader($cache));
        $collection->fromLoader(new DefaultRouteLoader(null, $resource));

        $this->assertCount(2, $collection->getRoutes());
        $this->assertEquals([$cacheRoute, $resourceRoute], $collection->getRoutes());
    }

    public function test_it_can_load_from_multiple_sources()
    {
        $cacheRoute = RouteBuilder::get('/')->build();
        $resourceRoute = RouteBuilder::post('/path')->build();
        $addRoute = RouteBuilder::put('/another')->build();
        $manyRoutes = [RouteBuilder::patch('/test')->build(), RouteBuilder::delete('/delete')->build()];

        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(new ArrayRouteCache([$cacheRoute]))
        );

        $collection->fromLoader(new DefaultRouteLoader(null, new ArrayRouteResource([$resourceRoute])));
        $collection->add($addRoute);
        $collection->addMany($manyRoutes);

        $this->assertCount(5, $collection->getRoutes());
        $this->assertEquals([$cacheRoute, $resourceRoute, $addRoute, ...$manyRoutes], $collection->getRoutes());
    }

    public function test_it_can_cache_its_routes()
    {
        $manyRoutes = [RouteBuilder::patch('/test')->build(), RouteBuilder::delete('/delete')->build()];

        $cache = new ArrayRouteCache();

        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader($cache)
        );

        $collection->addMany($manyRoutes);
        $collection->cache();

        $this->assertEquals($manyRoutes, $cache->get());
    }

    public function test_it_can_parse_routes()
    {
        $route = RouteBuilder::patch('/test')->build();

        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser())
        );

        $collection->add($route);

        $collection->parse();

        $this->assertEquals([], $collection->getRoutes());
    }

    public function test_it_can_match_routes()
    {
        $route = RouteBuilder::get('/test')->build();

        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser())
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

        $collection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser())
        );

        $this->assertFalse(
            $collection->match(
                new Request('get', '/test', '', '')
            )
        );
    }
}
