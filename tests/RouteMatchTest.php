<?php

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use LukasJankowski\Routing\RouteMatch;
use LukasJankowski\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouteMatchTest extends TestCase
{
    public function test_it_contains_the_matching_information()
    {
        $match = new RouteMatch(
            '/',
            '/',
            ['some', 'action'],
            'name',
            ['test_middleware'],
            ['param' => 'value']
        );

        $this->assertEquals('/', $match->getPath());
        $this->assertEquals('/', $match->getRoute());
        $this->assertEquals('name', $match->getName());
        $this->assertEquals(['test_middleware'], $match->getMiddlewares());
        $this->assertEquals(['some', 'action'], $match->getAction());
        $this->assertEquals(['param' => 'value'], $match->getParameters());
    }
}
