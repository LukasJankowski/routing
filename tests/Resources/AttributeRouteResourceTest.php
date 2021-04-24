<?php

namespace Resources;

use InvalidArgumentException;
use LukasJankowski\Routing\Resources\AttributeRouteResource;
use LukasJankowski\Routing\RouteBuilder;
use LukasJankowski\Routing\Tests\fixtures\AttributeClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AttributeRouteResourceTest extends TestCase
{
    public function test_it_throws_an_exception_if_php_version_mismatch()
    {
        if (PHP_MAJOR_VERSION >= 8) {
            $this->markTestSkipped();
        }

        $this->expectException(RuntimeException::class);

        new AttributeRouteResource([AttributeClass::class]);
    }

    public function test_it_can_retrieve_the_routes_from_resource()
    {
        $resource = new AttributeRouteResource([AttributeClass::class]);

        $this->assertEquals(
            [
                RouteBuilder::get('/', [AttributeClass::class, 'method'])
                    ->name('name')
                    ->host('host.com')
                    ->scheme('https')
                    ->constraint('to', '\d+')
                    ->middleware('test_middleware')
                    ->default(['to' => 'default'])
                    ->build(),
                RouteBuilder::match(['post', 'put'], '/route', [AttributeClass::class, 'test'])->build(),
                RouteBuilder::get('/test1', [AttributeClass::class, 'multiple'])->build(),
                RouteBuilder::get('/test2', [AttributeClass::class, 'multiple'])->build(),
            ],
            $resource->get()
        );
    }

    public function test_it_throws_an_exception_on_invalid_class()
    {
        $this->expectException(InvalidArgumentException::class);

        $resource = new AttributeRouteResource(['SomeClass\Namespace']);
        $resource->get();
    }
}
