<?php

namespace Resources;

use InvalidArgumentException;
use LukasJankowski\Routing\Resources\AttributeRouteResource;
use LukasJankowski\Routing\Route;
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
                (new Route('get', '/'))->action([AttributeClass::class, 'method']),
                (new Route(['post', 'put'], '/route'))->action([AttributeClass::class, 'test']),
                (new Route('get', '/test1'))->action([AttributeClass::class, 'multiple']),
                (new Route('get', '/test2'))->action([AttributeClass::class, 'multiple'])
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
