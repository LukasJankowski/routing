<?php

namespace Utilities;

use InvalidArgumentException;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Utilities\Method;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{
    public function provideMethods(): array
    {
        return [
            [
                'given' => 'gEt',
                'expected' => 'GET',
            ],
            [
                'given' => ['post', 'GET'],
                'expected' => ['POST', 'GET'],
            ],
            [
                'given' => 'ANY',
                'expected' => Request::METHODS,
            ],
        ];
    }

    /**
     * @dataProvider provideMethods
     */
    public function test_it_normalizes_methods($given, $expected)
    {
        $this->assertEquals($expected, Method::normalize($given));
    }

    public function test_it_throws_an_exception_on_invalid_method()
    {
        $this->expectException(InvalidArgumentException::class);

        Method::normalize('unknown_method');
    }
}
