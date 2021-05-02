<?php

namespace Loaders\Yaml;

use ErrorException;
use InvalidArgumentException;
use LukasJankowski\Routing\Loaders\Yaml\YamlResource;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class YamlResourceTest extends TestCase
{
    public function test_it_can_retrieve_the_routes_from_resource()
    {
        $resource = new YamlResource(__DIR__ . '/../../fixtures/routes.yml');

        $this->assertEquals(
            [
                RouteBuilder::get('/')->build(),
                RouteBuilder::match(['get', 'post'], '/path/{var}/{*?wc}', 'ControllerClass@method')
                    ->name('route.name')
                    ->constraint('var', '\d+')
                    ->middleware(['test_middleware', 'test_middleware_2'])
                    ->default(['wc' => ['some', 'segments']])
                    ->build(),
                RouteBuilder::match(['put', 'patch'], '/prefix/grouped')
                    ->name('prefix.grouped')
                    ->middleware(['prefix_middleware', 'grouped_middleware'])
                    ->build(),
                RouteBuilder::get('/prefix/another')
                    ->name('prefix.')
                    ->middleware('prefix_middleware')
                    ->build(),
                RouteBuilder::get('/prefix/nested/path')
                    ->name('prefix.')
                    ->middleware('prefix_middleware')
                    ->build(),
            ],
            $resource->get()
        );
    }

    public function test_it_throws_an_exception_on_invalid_file()
    {
        $this->expectException(InvalidArgumentException::class);

        new YamlResource('not_found');
    }

    public function test_it_throws_an_exception_if_no_array_is_returned()
    {
        $this->expectException(ErrorException::class);

        $resource = new YamlResource(__DIR__ . '/../../fixtures/invalid_routes.yml');
        $resource->get();
    }
}
