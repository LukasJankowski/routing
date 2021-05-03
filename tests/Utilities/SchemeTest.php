<?php

namespace Utilities;

use InvalidArgumentException;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Utilities\Scheme;
use PHPUnit\Framework\TestCase;

class SchemeTest extends TestCase
{
    public function provideSchemes(): array
    {
        return [
            [
                'given' => 'hTTps',
                'expected' => 'HTTPS'
            ],
            [
                'given' => ['http', 'HTTPS'],
                'expected' => ['HTTP', 'HTTPS']
            ],
            [
                'given' => '',
                'expected' => '',
            ],
            [
                'given' => [],
                'expected' => Request::SCHEMES
            ]
        ];
    }

    /**
     * @dataProvider provideSchemes
     */
    public function test_it_normalizes_schemes($given, $expected)
    {
        $this->assertEquals($expected, Scheme::normalize($given));
    }

    public function test_it_throws_exception_on_unknown_scheme()
    {
        $this->expectException(InvalidArgumentException::class);

        Scheme::normalize('unknown_scheme');
    }
}
