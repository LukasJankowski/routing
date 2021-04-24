<?php

namespace Loaders\Attribute;

use InvalidArgumentException;
use LukasJankowski\Routing\Loaders\Attribute\AttributeResource;
use LukasJankowski\Routing\RouteBuilder;
use LukasJankowski\Routing\Tests\fixtures\AlternateAttributeClass;
use LukasJankowski\Routing\Tests\fixtures\AttributeClass;
use PHPUnit\Framework\TestCase;

class AttributeResourceTest extends TestCase
{
    public function test_it_can_retrieve_the_routes_from_resource()
    {
        $resource = new AttributeResource([AttributeClass::class]);

        $this->assertEquals(
            [
                RouteBuilder::get('/prefix', [AttributeClass::class, 'method'])
                    ->name('prefix.name')
                    ->host('host.com')
                    ->scheme('https')
                    ->constraint('to', '\d+')
                    ->middleware(['prefix', 'test_middleware'])
                    ->default(['to' => 'default'])
                    ->build(),
                RouteBuilder::match(['post', 'put'], '/prefix/route', [AttributeClass::class, 'test'])
                    ->name('prefix.')
                    ->middleware('prefix')
                    ->build(),
                RouteBuilder::get('/prefix/test1', [AttributeClass::class, 'multiple'])
                    ->name('prefix.')
                    ->middleware('prefix')
                    ->build(),
                RouteBuilder::get('/prefix/test2', [AttributeClass::class, 'multiple'])
                    ->name('prefix.')
                    ->middleware('prefix')
                    ->build(),
            ],
            $resource->get()
        );
    }

    public function test_it_can_retrieve_attributes_without_group()
    {
        $resource = new AttributeResource([AlternateAttributeClass::class]);

        $this->assertEquals(
            [RouteBuilder::get('/', [AlternateAttributeClass::class, 'method'])->build()],
            $resource->get()
        );
    }


    public function test_it_throws_an_exception_on_invalid_class()
    {
        $this->expectException(InvalidArgumentException::class);

        $resource = new AttributeResource(['SomeClass\Namespace']);
        $resource->get();
    }
}
