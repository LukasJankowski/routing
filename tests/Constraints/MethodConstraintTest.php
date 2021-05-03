<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class MethodConstraintTest extends TestCase
{
    public function test_it_returns_an_error_message()
    {
        $constraint = new MethodConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new MethodConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function provideValidMethods(): array
    {
        return [
            ['request' => 'get', 'route' => 'get'],
            ['request' => 'post', 'route' => 'post'],
            ['request' => 'put', 'route' => ['post', 'put', 'patch']],
            ['request' => 'head', 'route' => ['head']],
        ];
    }

    public function provideInvalidMethods(): array
    {
        return [
            ['request' => 'get', 'route' => 'post'],
            ['request' => 'post', 'route' => 'patch'],
            ['request' => 'put', 'route' => 'get'],
            ['request' => 'patch', 'route' => ['post', 'get']],
            ['request' => 'head', 'route' => ['get']],
        ];
    }

    /**
     * @dataProvider provideValidMethods
     */
    public function test_it_validates_methods($requestMethod, $route)
    {
        $constraint = new MethodConstraint();

        $request = new Request($requestMethod, '/', '', '');

        $constraint->setRequest($request);
        $constraint->setRoute(RouteBuilder::match((array) $route, '/')->build());

        $this->assertTrue($constraint->validate());
    }

    /**
     * @dataProvider provideInvalidMethods
     */
    public function test_it_validates_invalid_methods($requestMethod, $route)
    {
        $constraint = new MethodConstraint();

        $request = new Request($requestMethod, '/', '', '');

        $constraint->setRequest($request);
        $constraint->setRoute(RouteBuilder::match((array) $route, '/')->build());

        $this->assertFalse($constraint->validate());
    }
}
