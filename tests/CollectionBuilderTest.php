<?php

use LukasJankowski\Routing\Handlers\DefaultHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Handlers\Regex\RegexMatcher;
use LukasJankowski\Routing\Handlers\Regex\RegexParser;
use LukasJankowski\Routing\Loaders\DefaultLoader;
use LukasJankowski\Routing\Loaders\Fake\FakeCache;
use LukasJankowski\Routing\Loaders\Fake\FakeResource;
use LukasJankowski\Routing\Collection;
use LukasJankowski\Routing\CollectionBuilder;
use PHPUnit\Framework\TestCase;

class CollectionBuilderTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $this->assertInstanceOf(CollectionBuilder::class, CollectionBuilder::handler('regex'));
    }

    public function test_it_can_create_collections_with_handlers()
    {
        $manualCollection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );
        $collection = CollectionBuilder::handler(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        )->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_regex_collections()
    {
        $manualCollection = new Collection(
            new DefaultHandler(new RegexMatcher(), new RegexParser())
        );
        $collection = CollectionBuilder::handler('regex')->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_fake_collections()
    {
        $manualCollection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser())
        );
        $collection = CollectionBuilder::handler('fake')->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_loaders()
    {
        $manualCollection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new FakeCache(), new FakeResource())
        );
        $collection = CollectionBuilder::handler('fake')
            ->loader(new DefaultLoader(new FakeCache(), new FakeResource()))
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_manually_defined_loaders()
    {
        $manualCollection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new FakeCache(), new FakeResource())
        );
        $collection = CollectionBuilder::handler('fake')
            ->cache(new FakeCache())
            ->resource(new FakeResource())
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_partial_defined_loaders()
    {
        $manualCollection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(new FakeCache())
        );
        $collection = CollectionBuilder::handler('fake')
            ->cache(new FakeCache())
            ->build();

        $this->assertEquals($manualCollection, $collection);


        $manualCollection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            new DefaultLoader(null, new FakeResource())
        );
        $collection = CollectionBuilder::handler('fake')
            ->resource(new FakeResource())
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_can_create_collections_with_custom_names()
    {
        $manualCollection = new Collection(
            new DefaultHandler(new FakeMatcher(), new FakeParser()),
            null,
            'some-collection'
        );
        $collection = CollectionBuilder::handler('fake')
            ->name('some-collection')
            ->build();

        $this->assertEquals($manualCollection, $collection);
    }

    public function test_it_throws_an_exception_on_unknown_default_handler()
    {
        $this->expectException(InvalidArgumentException::class);

        CollectionBuilder::handler('unknown');
    }
}
