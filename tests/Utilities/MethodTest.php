<?php

namespace Utilities;

use InvalidArgumentException;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Utilities\Method;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{
    public function test_it_normalizes_methods()
    {
        $this->assertEquals('GET', Method::normalize('gEt'));
        $this->assertEquals(['POST', 'GET'], Method::normalize(['post', 'GET']));
        $this->assertEquals(Request::METHODS, Method::normalize('ANY'));

        $this->expectException(InvalidArgumentException::class);

        Method::normalize('unknown_method');
    }
}
