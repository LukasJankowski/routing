<?php

use LukasJankowski\Routing\Constraints\FakeConstraint;
use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Constraints\SegmentConstraint;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class RouteBuilderTest extends TestCase
{
    public function test_it_can_build_a_route()
    {
        $route = RouteBuilder::get('/{var}/path')
            ->action(['some', 'callback'])
            ->name('name')
            ->host('host.com')
            ->scheme('https')
            ->middleware('test_middleware')
            ->default(['to' => 'default'])
            ->constraint('to', '\d+')
            ->build();

        $this->assertEquals(['GET'], $route->getMethods());
        $this->assertEquals('/{var}/path', $route->getPath());
        $this->assertEquals(['some', 'callback'], $route->getAction());
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

    public function test_it_can_build_multiple_segment_constraints()
    {
        $route = RouteBuilder::get('/')
            ->constraint('to', '\d+')
            ->constraint('from', '\d+')
            ->build();

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
        $route = RouteBuilder::get('/')
            ->constraint(FakeConstraint::class, 'any-value')
            ->constraint([FakeConstraint::class => ['another-value']])
            ->build();

        $this->assertEquals(['another-value'], $route->getConstraints(FakeConstraint::class));
    }

    public function test_it_can_construct_routes_with_methods_as_arguments()
    {
        $route = RouteBuilder::get('/')->build();
        $this->assertEquals(['GET'], $route->getMethods());

        $route = RouteBuilder::post('/')->build();
        $this->assertEquals(['POST'], $route->getMethods());

        $route = RouteBuilder::put('/')->build();
        $this->assertEquals(['PUT'], $route->getMethods());

        $route = RouteBuilder::patch('/')->build();
        $this->assertEquals(['PATCH'], $route->getMethods());

        $route = RouteBuilder::delete('/')->build();
        $this->assertEquals(['DELETE'], $route->getMethods());

        $route = RouteBuilder::head('/')->build();
        $this->assertEquals(['HEAD'], $route->getMethods());

        $route = RouteBuilder::options('/')->build();
        $this->assertEquals(['OPTIONS'], $route->getMethods());

        $route = RouteBuilder::match(['get', 'post'], '/')->build();
        $this->assertEquals(['GET', 'POST'], $route->getMethods());
    }

    public function test_it_can_use_groups()
    {
        RouteBuilder::group(['path' => '/prefix', 'name' => 'prefix.', 'middleware' => ['prefix']], function () {
            $route1 = RouteBuilder::get('/path1')
                ->name('name1')
                ->middleware('middleware1')
                ->build();

            $this->assertEquals('/prefix/path1', $route1->getPath());
            $this->assertEquals('prefix.name1', $route1->getName());
            $this->assertEquals(['prefix', 'middleware1'], $route1->getMiddlewares());

            RouteBuilder::group(['path' => '/another', 'name' => 'another.', 'middleware' => ['another']], function () {
                $route1 = RouteBuilder::get('/path1')
                    ->name('name1')
                    ->middleware('middleware1')
                    ->build();

                $this->assertEquals('/another/prefix/path1', $route1->getPath());
                $this->assertEquals('another.prefix.name1', $route1->getName());
                $this->assertEquals(['another', 'prefix', 'middleware1'], $route1->getMiddlewares());
            });

            $route2 = RouteBuilder::get('/path2')
                ->name('name2')
                ->middleware('middleware2')
                ->build();

            $this->assertEquals('/prefix/path2', $route2->getPath());
            $this->assertEquals('prefix.name2', $route2->getName());
            $this->assertEquals(['prefix', 'middleware2'], $route2->getMiddlewares());
        });

        $route1 = RouteBuilder::get('/path1')
            ->name('name1')
            ->middleware('middleware1')
            ->build();

        $this->assertEquals('/path1', $route1->getPath());
        $this->assertEquals('name1', $route1->getName());
        $this->assertEquals(['middleware1'], $route1->getMiddlewares());
    }
}
