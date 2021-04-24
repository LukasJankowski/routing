<?php

use LukasJankowski\Routing\Constraints\FakeRouteConstraint;
use LukasJankowski\Routing\Constraints\HostRouteConstraint;
use LukasJankowski\Routing\Constraints\MethodRouteConstraint;
use LukasJankowski\Routing\Constraints\SchemeRouteConstraint;
use LukasJankowski\Routing\Constraints\SegmentRouteConstraint;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $route = new Route('/');

        $this->assertInstanceOf(Route::class, $route);
    }

    public function test_it_can_be_constructed()
    {
        $route = new Route(
            '/{var}/path',
        [$this, 'test_it_can_be_constructed'],
            'name',
            [
                MethodRouteConstraint::class => ['GET'],
                HostRouteConstraint::class => 'host.com',
                SchemeRouteConstraint::class => ['HTTPS'],
                SegmentRouteConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
            ],
            ['test_middleware'],
            ['to' => 'default']
        );

        $this->assertEquals(['GET'], $route->getMethods());
        $this->assertEquals('/{var}/path', $route->getPath());
        $this->assertEquals([$this, 'test_it_can_be_constructed'], $route->getAction());
        $this->assertEquals('name', $route->getName());
        $this->assertEquals('host.com', $route->getHost());
        $this->assertEquals(['HTTPS'], $route->getSchemes());
        $this->assertEquals(
            [
                MethodRouteConstraint::class => ['GET'],
                HostRouteConstraint::class => 'host.com',
                SchemeRouteConstraint::class => ['HTTPS'],
                SegmentRouteConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
            ],
            $route->getConstraints()
        );
        $this->assertEquals(['test_middleware'], $route->getMiddlewares());
        $this->assertEquals(['to' => 'default'], $route->getDefaults());
        $this->assertNull($route->parsedPath);
        $this->assertEmpty($route->parsedParameters);
    }

    public function test_it_can_be_serialized()
    {
        $route = new Route(
            '/{var}/path',
            [FakeRouteConstraint::class, 'validate'],
            'name',
            [
                MethodRouteConstraint::class => ['GET'],
                HostRouteConstraint::class => 'host.com',
                SchemeRouteConstraint::class => ['HTTPS'],
                SegmentRouteConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
            ],
            ['test_middleware'],
            ['to' => 'default']
        );

        $route = unserialize(serialize($route));

        $this->assertEquals(['GET'], $route->getMethods());
        $this->assertEquals('/{var}/path', $route->getPath());
        $this->assertEquals([FakeRouteConstraint::class, 'validate'], $route->getAction());
        $this->assertEquals('name', $route->getName());
        $this->assertEquals('host.com', $route->getHost());
        $this->assertEquals(['HTTPS'], $route->getSchemes());
        $this->assertEquals(
            [
                MethodRouteConstraint::class => ['GET'],
                HostRouteConstraint::class => 'host.com',
                SchemeRouteConstraint::class => ['HTTPS'],
                SegmentRouteConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
            ],
            $route->getConstraints()
        );
        $this->assertEquals(['test_middleware'], $route->getMiddlewares());
        $this->assertEquals(['to' => 'default'], $route->getDefaults());
        $this->assertNull($route->parsedPath);
        $this->assertEmpty($route->parsedParameters);
    }

    public function test_it_throws_an_exception_when_serializing_closures()
    {
        $route = new Route('/{var}/path', function() {});

        $this->expectExceptionMessage("Serialization of 'Closure' is not allowed");

        serialize($route);
    }
}
