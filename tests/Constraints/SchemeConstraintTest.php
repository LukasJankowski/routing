<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class SchemeConstraintTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $constraint = new SchemeConstraint();
        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute(new Route('get', '/'));

        $this->assertInstanceOf(SchemeConstraint::class, $constraint);
    }

    public function test_it_returns_an_error_message()
    {
        $constraint = new SchemeConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new SchemeConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function test_it_validates_the_scheme()
    {
        $constraint = new SchemeConstraint();

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
