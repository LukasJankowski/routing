<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class HostConstraintTest extends TestCase
{
    public function test_it_returns_an_error_message()
    {
        $constraint = new HostConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new HostConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function provideValidHost(): array
    {
        return [
            ['request' => 'test.com', 'route' => 'test.com'],
            ['request' => 'api.test.com', 'route' => 'api.test.com'],
        ];
    }

    public function provideInvalidHost(): array
    {
        return [
            ['request' => 'test.com', 'route' => 'another.com'],
            ['request' => '', 'route' => 'another.com'],
            ['request' => 'api.test.com', 'route' => 'mail.test.com'],
        ];
    }

    /**
     * @dataProvider provideValidHost
     */
    public function test_it_validates_host($requestHost, $route)
    {
        $constraint = new HostConstraint();

        $request = new Request('get', '/', '', '');
        $request->host = $requestHost;

        $constraint->setRequest($request);
        $constraint->setRoute(RouteBuilder::get('/')->host($route)->build());

        $this->assertTrue($constraint->validate());
    }

    /**
     * @dataProvider provideInvalidHost
     */
    public function test_it_validates_invalid_host($requestHost, $route)
    {
        $constraint = new HostConstraint();

        $request = new Request('get', '/', '', '');
        $request->host = $requestHost;

        $constraint->setRequest($request);
        $constraint->setRoute(RouteBuilder::get('/')->host($route)->build());

        $this->assertFalse($constraint->validate());
    }
}
