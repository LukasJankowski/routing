<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\MethodRouteConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class MethodRouteConstraintTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $constraint = new MethodRouteConstraint();
        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute(new Route('get', '/'));

        $this->assertInstanceOf(MethodRouteConstraint::class, $constraint);
    }

    public function test_it_returns_an_error_message()
    {
        $constraint = new MethodRouteConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new MethodRouteConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function test_it_validates_the_scheme()
    {
        $constraint = new MethodRouteConstraint();

        $valid = [
            'get' => 'get',
            'post' => 'post',
            'put' => ['post', 'put', 'patch'],
            'purge' => ['purge']
        ];

        $invalid = [
            'get' => 'post',
            'post' => 'patch',
            'put' => 'get',
            'patch' => ['post', 'get'],
            'purge' => ['get'],
        ];

        foreach ($valid as $requestMethod => $routeMethod) {
            $request = new Request($requestMethod, '/', '', '');

            $constraint->setRequest($request);
            $constraint->setRoute(new Route($routeMethod, '/'));

            $this->assertTrue($constraint->validate());
        }


        foreach ($invalid as $requestMethod => $routeMethod) {
            $request = new Request($requestMethod, '/', '', '');

            $constraint->setRequest($request);
            $constraint->setRoute(new Route($routeMethod, '/'));

            $this->assertFalse($constraint->validate());
        }
    }
}
