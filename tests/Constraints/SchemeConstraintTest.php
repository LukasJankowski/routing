<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class SchemeConstraintTest extends TestCase
{
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

    public function provideValidSchemes(): array
    {
        return [
            ['request' => '', 'route' => 'http'],
            ['request' => 'HTTP', 'route' => 'http'],
            ['request' => 'HTTPS', 'route' => ['http', 'https']],
        ];
    }

    public function provideInvalidSchemes(): array
    {
        return [
            ['request' => 'HTTPS', 'route' => 'http'],
            ['request' => 'HTTP', 'route' => ['https']],
        ];
    }

    /**
     * @dataProvider provideValidSchemes
     */
    public function test_it_validates_schemes($requestScheme, $route)
    {
        $constraint = new SchemeConstraint();

        $request = new Request('get', '/', '', '');
        $request->scheme = $requestScheme;

        $constraint->setRequest($request);
        $constraint->setRoute(RouteBuilder::get('/')->scheme($route)->build());

        $this->assertTrue($constraint->validate());
    }

    /**
     * @dataProvider provideInvalidSchemes
     */
    public function test_it_validates_invalid_schemes($requestScheme, $route)
    {
        $constraint = new SchemeConstraint();

        $request = new Request('get', '/', '', '');
        $request->scheme = $requestScheme;

        $constraint->setRequest($request);
        $constraint->setRoute(RouteBuilder::get('/')->scheme($route)->build());

        $this->assertFalse($constraint->validate());
    }
}
