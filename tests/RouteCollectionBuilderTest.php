<?php

use LukasJankowski\Routing\Handlers\DefaultRouteHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteParser;
use LukasJankowski\Routing\Handlers\Regex\RegexRouteMatcher;
use LukasJankowski\Routing\Handlers\Regex\RegexRouteParser;
use LukasJankowski\Routing\Loaders\DefaultRouteLoader;
use LukasJankowski\Routing\Loaders\Fake\FakeRouteCache;
use LukasJankowski\Routing\Loaders\Fake\FakeRouteResource;
use LukasJankowski\Routing\RouteCollection;
use LukasJankowski\Routing\RouteCollectionBuilder;
use PHPUnit\Framework\TestCase;

class RouteCollectionBuilderTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $this->assertInstanceOf(RouteCollectionBuilder::class, RouteCollectionBuilder::handler('regex'));
    }

    public function test_it_can_create_collections_with_handlers()
    {
        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser())
        );
        $collection = RouteCollectionBuilder::handler(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser())
        )->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_regex_collections()
    {
        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new RegexRouteMatcher(), new RegexRouteParser())
        );
        $collection = RouteCollectionBuilder::handler('regex')->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_fake_collections()
    {
        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser())
        );
        $collection = RouteCollectionBuilder::handler('fake')->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_loaders()
    {
        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(new FakeRouteCache(), new FakeRouteResource())
        );
        $collection = RouteCollectionBuilder::handler('fake')
            ->loader(new DefaultRouteLoader(new FakeRouteCache(), new FakeRouteResource()))
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_manually_defined_loaders()
    {
        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(new FakeRouteCache(), new FakeRouteResource())
        );
        $collection = RouteCollectionBuilder::handler('fake')
            ->cache(new FakeRouteCache())
            ->resource(new FakeRouteResource())
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_partial_defined_loaders()
    {
        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(new FakeRouteCache())
        );
        $collection = RouteCollectionBuilder::handler('fake')
            ->cache(new FakeRouteCache())
            ->build();

        $this->assertEquals($manualCollection, $collection);


        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            new DefaultRouteLoader(null, new FakeRouteResource())
        );
        $collection = RouteCollectionBuilder::handler('fake')
            ->resource(new FakeRouteResource())
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_custom_names()
    {
        $manualCollection = new RouteCollection(
            new DefaultRouteHandler(new FakeRouteMatcher(), new FakeRouteParser()),
            null,
            'some-collection'
        );
        $collection = RouteCollectionBuilder::handler('fake')
            ->name('some-collection')
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_throws_an_exception_on_unknown_default_handler()
    {
        $this->expectException(InvalidArgumentException::class);

        RouteCollectionBuilder::handler('unknown');
    }
}
