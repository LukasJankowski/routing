<?php

namespace Loaders\Php;

use ErrorException;
use InvalidArgumentException;
use LukasJankowski\Routing\Loaders\Php\PhpResource;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class PhpResourceTest extends TestCase
{
    public function test_it_can_retrieve_the_routes_from_resource_array()
    {
        $resource = new PhpResource(__DIR__ . '/../../fixtures/routes.php');

        $this->assertEquals(
            [
                RouteBuilder::get('/')->build(),
                RouteBuilder::post('/another')->build(),
            ],
            $resource->get()
        );
    }

    /**
     * @runInSeparateProcess
     */
    public function test_it_can_retrieve_routes_by_using_static_collections()
    {
        $resource = new PhpResource(__DIR__ . '/../../fixtures/grouped_routes.php');

        $this->assertEquals(
            [
                RouteBuilder::get('/')->build(),
                RouteBuilder::post('/another')->build(),
                RouteBuilder::get('/prefix/nested')->build(),
            ],
            $resource->get()
        );
    }

    public function test_it_throws_an_exception_on_invalid_file()
    {
        $this->expectException(InvalidArgumentException::class);

        new PhpResource('not_found');
    }

    public function test_it_throws_an_exception_if_no_array_is_returned_and_it_doesnt_use_a_static_collection()
    {
        $this->expectException(ErrorException::class);

        $resource = new PhpResource(__DIR__ . '/../../fixtures/invalid_routes.php');
        $resource->get();
    }
}
