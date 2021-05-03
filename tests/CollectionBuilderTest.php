<?php

use LukasJankowski\Routing\Collection;
use LukasJankowski\Routing\CollectionBuilder;
use LukasJankowski\Routing\Handlers\DefaultHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Handlers\Fixed\FixedMatcher;
use LukasJankowski\Routing\Handlers\Fixed\FixedParser;
use LukasJankowski\Routing\Handlers\Regex\RegexMatcher;
use LukasJankowski\Routing\Handlers\Regex\RegexParser;
use LukasJankowski\Routing\Loaders\Array\ArrayCache;
use LukasJankowski\Routing\Loaders\Array\ArrayResource;
use LukasJankowski\Routing\Loaders\DefaultLoader;
use LukasJankowski\Routing\Loaders\Fake\FakeCache;
use LukasJankowski\Routing\Loaders\Fake\FakeResource;
use PHPUnit\Framework\TestCase;

class CollectionBuilderTest extends TestCase
{
    public function provideHandlerClasses(): array
    {
        return [
            ['regex', new RegexMatcher(), new RegexParser()],
            ['fake', new FakeMatcher(), new FakeParser()],
            ['fixed', new FixedMatcher(), new FixedParser()],
        ];
    }

    public function test_it_can_be_instantiated()
    {
        $this->assertInstanceOf(CollectionBuilder::class, CollectionBuilder::handler('regex'));
    }

    /**
     * @dataProvider provideHandlerClasses
     */
    public function test_it_can_create_predefined_handlers($name, $matcher, $parser)
    {
        $this->assertEquals(
            new Collection(new DefaultHandler($matcher, $parser)),
            CollectionBuilder::handler($name)->build()
        );
    }

    public function provideLoaderClasses(): array
    {
        return [
            ['array', new ArrayCache(), new ArrayResource()],
            ['fake', new FakeCache(), new FakeResource()],
        ];
    }

    /**
     * @dataProvider provideLoaderClasses
     */
    public function test_it_can_create_predefined_loaders($name, $cache, $resource)
    {
        $this->assertEquals(
            new Collection(
                new DefaultHandler(new FakeMatcher(), new FakeParser()),
                new DefaultLoader($cache, $resource)
            ),
            CollectionBuilder::handler('fake')->loader($name)->build()
        );
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

    public function test_it_throws_an_exception_on_unknown_default_loader()
    {
        $this->expectException(InvalidArgumentException::class);

        CollectionBuilder::handler('fake')->loader('unknown');
    }
}
