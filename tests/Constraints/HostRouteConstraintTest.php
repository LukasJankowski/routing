<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\HostRouteConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class HostRouteConstraintTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $constraint = new HostRouteConstraint();
        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute(new Route('get', '/'));

        $this->assertInstanceOf(HostRouteConstraint::class, $constraint);
    }

    public function test_it_returns_an_error_message()
    {
        $constraint = new HostRouteConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new HostRouteConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function test_it_validates_the_scheme()
    {
        $constraint = new HostRouteConstraint();

        $valid = [
            'test.com' => 'test.com',
            'api.test.com' => 'api.test.com'
        ];

        $invalid = [
            'test.com' => 'another.com',
            '' => 'another.com',
            'api.test.com' => 'mail.test.com',
        ];

        foreach ($valid as $requestHost => $routeHost) {
            $request = new Request('get', '/', $requestHost, '');
            $request->host = $requestHost;

            $constraint->setRequest($request);
            $constraint->setRoute(RouteBuilder::get('/')->host($routeHost)->build());

            $this->assertTrue($constraint->validate());
        }


        foreach ($invalid as $requestHost => $routeHost) {
            $request = new Request('get', '/', $requestHost, '');
            $request->host = $requestHost;

            $constraint->setRequest($request);
            $constraint->setRoute(RouteBuilder::get('/')->host($routeHost)->build());

            $this->assertFalse($constraint->validate());
        }
    }
}
