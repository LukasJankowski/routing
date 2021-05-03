<?php

use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Constraints\SegmentConstraint;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    private Route $route;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->route = new Route(
            '/{var}/path',
            'SomeAction@handle',
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
    }

    /**
     * Validate the route's contents.
     */
    private function validateRoute(Route $route): void
    {
        $this->assertEquals(['GET'], $route->getMethods());
        $this->assertEquals('/{var}/path', $route->getPath());
        $this->assertEquals('SomeAction@handle', $route->getAction());
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
        $this->assertNull($route->getPrepared());
        $this->assertEmpty($route->getParameters());
    }

    public function test_it_can_be_constructed()
    {
        $route = $this->route;

        $this->validateRoute($route);
    }

    public function test_it_can_be_serialized()
    {
        $route = $this->route;

        $route = unserialize(serialize($route));

        $this->validateRoute($route);
    }

    public function test_it_throws_an_exception_when_serializing_closures()
    {
        $route = new Route('/{var}/path', function () {
        });

        $this->expectExceptionMessage("Serialization of 'Closure' is not allowed");

        serialize($route);
    }
}
