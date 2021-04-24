<?php

namespace Loaders\Php;

use ErrorException;
use InvalidArgumentException;
use LukasJankowski\Routing\Loaders\Php\PhpResource;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class PhpResourceTest extends TestCase
{
    public function test_it_can_retrieve_the_routes_from_resource()
    {
        $resource = new PhpResource(__DIR__ . '/../../fixtures/routes.php');

        $this->assertEquals(
            [
                new Route('get', '/'),
                new Route('post', '/another')
            ],
            $resource->get()
        );
    }

    public function test_it_throws_an_exception_on_invalid_file()
    {
        $this->expectException(InvalidArgumentException::class);

        new PhpResource('not_found');
    }

    public function test_it_throws_an_exception_if_no_array_is_returned()
    {
        $this->expectException(ErrorException::class);

        $resource = new PhpResource(__DIR__ . '/../../fixtures/invalid_routes.php');
        $resource->get();
    }
}
