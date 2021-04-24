<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\SchemeRouteConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class SchemeRouteConstraintTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $constraint = new SchemeRouteConstraint();
        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute(new Route('get', '/'));

        $this->assertInstanceOf(SchemeRouteConstraint::class, $constraint);
    }

    public function test_it_returns_an_error_message()
    {
        $constraint = new SchemeRouteConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new SchemeRouteConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function test_it_validates_the_scheme()
    {
        $constraint = new SchemeRouteConstraint();

        $valid = [
            '' => 'http',
            'HTTP' => 'http',
            'HTTPS' => ['http', 'https']
        ];

        $invalid = [
            'HTTPS' => 'http',
            'HTTP' => ['https'],
        ];

        foreach ($valid as $requestScheme => $routeScheme) {
            $request = new Request('get', '/', '', $requestScheme);
            $request->scheme = $requestScheme;

            $constraint->setRequest($request);
            $constraint->setRoute(RouteBuilder::get('/')->scheme($routeScheme)->build());

            $this->assertTrue($constraint->validate());
        }


        foreach ($invalid as $requestScheme => $routeScheme) {
            $request = new Request('get', '/', '', $requestScheme);
            $request->scheme = $requestScheme;

            $constraint->setRequest($request);
            $constraint->setRoute(RouteBuilder::get('/')->scheme($routeScheme)->build());

            $this->assertFalse($constraint->validate());
        }
    }
}
