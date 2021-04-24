<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\FakeRouteConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class FakeRouteConstraintTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $constraint = new FakeRouteConstraint();
        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute(new Route('get', '/'));

        $this->assertInstanceOf(FakeRouteConstraint::class, $constraint);
    }

    public function test_it_returns_an_error_message()
    {
        $constraint = new FakeRouteConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new FakeRouteConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function test_it_validates_the_scheme()
    {
        $constraint = new FakeRouteConstraint();

        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute(RouteBuilder::get('/')->build());

        $this->assertTrue($constraint->validate());
        $this->assertFalse($constraint->validate(null, false));
    }
}
