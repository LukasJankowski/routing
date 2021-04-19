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
        $route = new Route('get', '/');

        $this->assertInstanceOf(Route::class, $route);
    }

    public function test_it_can_be_constructed()
    {
        $route = new Route(
            'get',
            '/{var}/path',
        [$this, 'test_it_can_be_constructed'],
            'name',
            'host.com',
            'https',
            ['to' => '\d+'],
            'test_middleware',
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

    public function test_it_can_be_constructed_fluid()
    {
        $route = (new Route(['get', 'post'], '/path/to'))
            ->action([$this, 'test_it_can_be_constructed_fluid'])
            ->name('name')
            ->host('host.com')
            ->scheme(['http', 'https'])
            ->constraint(['to' => '\d+'])
            ->middleware('test_middleware')
            ->default(['to' => 'default']);

        $this->assertEquals(['GET', 'POST'], $route->getMethods());
        $this->assertEquals('/path/to', $route->getPath());
        $this->assertEquals([$this, 'test_it_can_be_constructed_fluid'], $route->getAction());
        $this->assertEquals('name', $route->getName());
        $this->assertEquals('host.com', $route->getHost());
        $this->assertEquals(['HTTP', 'HTTPS'], $route->getSchemes());
        $this->assertEquals(
            [
                MethodRouteConstraint::class => ['GET', 'POST'],
                HostRouteConstraint::class => 'host.com',
                SchemeRouteConstraint::class => ['HTTP', 'HTTPS'],
                SegmentRouteConstraint::class => [['name' => 'to', 'pattern' => '\d+']]
            ],
            $route->getConstraints()
        );
        $this->assertEquals(['test_middleware'], $route->getMiddlewares());
        $this->assertEquals(['to' => 'default'], $route->getDefaults());
    }

    public function test_it_can_have_multiple_segment_constraints()
    {
        $route = new Route('get', '/');
        $route->constraint('to', '\d+');
        $route->constraint('from', '\d+');

        $this->assertEquals(
            [
                ['name' => 'to', 'pattern' => '\d+'],
                ['name' => 'from', 'pattern' => '\d+']
            ],
            $route->getSegmentConstraints()
        );
    }

    public function test_can_add_custom_constraints()
    {
        $route = new Route('get', '/');
        $route->constraint(FakeRouteConstraint::class, 'any-value');

        $this->assertEquals('any-value', $route->getConstraints(FakeRouteConstraint::class));

        $route->constraint([FakeRouteConstraint::class => ['another-value']]);
        $this->assertEquals(['another-value'], $route->getConstraints(FakeRouteConstraint::class));
    }

    public function test_it_throws_an_exception_if_no_action_defined_while_retrieving()
    {
        $route = new Route('get', '/');

        $this->expectException(RuntimeException::class);

        $route->getAction();
    }

    public function test_it_can_be_serialized()
    {
        $route = new Route(
            'get',
            '/{var}/path',
            [FakeRouteConstraint::class, 'validate'],
            'name',
            'host.com',
            'https',
            ['to' => '\d+'],
            'test_middleware',
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
}
