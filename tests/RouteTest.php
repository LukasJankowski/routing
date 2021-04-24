<?php

use LukasJankowski\Routing\Constraints\FakeConstraint;
use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Constraints\SegmentConstraint;
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
                MethodConstraint::class => ['GET'],
                HostConstraint::class => 'host.com',
                SchemeConstraint::class => ['HTTPS'],
                SegmentConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
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
                MethodConstraint::class => ['GET'],
                HostConstraint::class => 'host.com',
                SchemeConstraint::class => ['HTTPS'],
                SegmentConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
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
            [FakeConstraint::class, 'validate'],
            'name',
            [
                MethodConstraint::class => ['GET'],
                HostConstraint::class => 'host.com',
                SchemeConstraint::class => ['HTTPS'],
                SegmentConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
            ],
            ['test_middleware'],
            ['to' => 'default']
        );

        $route = unserialize(serialize($route));

        $this->assertEquals(['GET'], $route->getMethods());
        $this->assertEquals('/{var}/path', $route->getPath());
        $this->assertEquals([FakeConstraint::class, 'validate'], $route->getAction());
        $this->assertEquals('name', $route->getName());
        $this->assertEquals('host.com', $route->getHost());
        $this->assertEquals(['HTTPS'], $route->getSchemes());
        $this->assertEquals(
            [
                MethodConstraint::class => ['GET'],
                HostConstraint::class => 'host.com',
                SchemeConstraint::class => ['HTTPS'],
                SegmentConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
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
