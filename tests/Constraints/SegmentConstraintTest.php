<?php

namespace Constraints;

use LukasJankowski\Routing\Constraints\SegmentConstraint;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use PHPUnit\Framework\TestCase;

class SegmentConstraintTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $constraint = new SegmentConstraint();
        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute(new Route('get', '/'));

        $this->assertInstanceOf(SegmentConstraint::class, $constraint);
    }

    public function test_it_returns_an_error_message()
    {
        $constraint = new SegmentConstraint();

        $this->assertIsString($constraint->getErrorMessage());
    }

    public function test_it_returns_a_status_code()
    {
        $constraint = new SegmentConstraint();

        $this->assertIsInt($constraint->getErrorCode());
    }

    public function test_it_sets_the_parsed_parameters()
    {
        $route = new Route('get', '/');
        $route->setParameters([
            'var' => [
                'value' => 'string',
                'wildcard' => false,
            ],
            'opt' => [
                'value' => null,
                'wildcard' => false
            ],
            'test' => [
                'value' => ['test'],
                'wildcard' => false,
            ],
            'next' => [
                'value' => 'shouldbearray',
                'wildcard' => true,
            ],
            'set' => [
                'value' => 'array/segment',
                'wildcard' => true
            ],
            'null' => [
                'value' => [],
                'wildcard' => true,
            ],
            'unset' => [
                'value' => null,
                'wildcard' => true,
            ],
            'default' => [
                'value' => ['default'],
                'wildcard' => true,
            ],
        ]);


        $constraint = new SegmentConstraint();
        $constraint->setRequest(new Request('get', '/', '', ''));
        $constraint->setRoute($route);

        $constraint->validate();

        $this->assertEquals(
            [
                'var' => 'string',
                'opt' => null,
                'test' => ['test'],
                'next' => ['shouldbearray'],
                'set' => ['array', 'segment'],
                'null' => [],
                'unset' => null,
                'default' => ['default'],
            ],
            $route->getParameters()
        );
    }
}
