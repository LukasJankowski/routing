<?php

use LukasJankowski\Routing\Link;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public function provideValidLinks(): array
    {
        return [
            [
                'path' => '/',
                'expected' => '/',
                'params' => []
            ],
            [
                'path' => '/path',
                'expected' => '/path',
                'params' => []
            ],
            [
                'path' => '/{var}',
                'expected' => '/anything',
                'params' => ['var' => 'anything']
            ],
            [
                'path' => '/nested/{var}',
                'expected' => '/nested/anything',
                'params' => ['var' => 'anything']
            ],
            [
                'path' => '/{var}/nested',
                'expected' => '/anything/nested',
                'params' => ['var' => 'anything']
            ],
            [
                'path' => '/{double}/{var}',
                'expected' => '/anything/nested',
                'params' => ['double' => 'anything', 'var' => 'nested']
            ],
            [
                'path' => '/in/{between}/nested',
                'expected' => '/in/anything/nested',
                'params' => ['between' => 'anything']
            ],
            [
                'path' => '/{var:\d+}',
                'expected' => '/123123',
                'params' => ['var' => '123123']
            ],
            [
                'path' => '/{?var}',
                'expected' => '/',
                'params' => []
            ],
            [
                'path' => '/{?var}/static',
                'expected' => '/static',
                'params' => []
            ],
            [
                'path' => '/{?var}/{?test}',
                'expected' => '/',
                'params' => []
            ],
            [
                'path' => '/{*var}',
                'expected' => '/anything/more/test',
                'params' => ['var' => ['anything', 'more', 'test']]
            ],
            [
                'path' => '/static/{*var}',
                'expected' => '/static/anything/more/test',
                'params' => ['var' => ['anything', 'more', 'test']]
            ],
            [
                'path' => '/{*?var}',
                'expected' => '/',
                'params' => []
            ],
            [
                'path' => '/long/static/part/{*wc}/{var}',
                'expected' => '/long/static/part/wildcard/segments/variable',
                'params' => ['wc' => ['wildcard', 'segments'], 'var' => 'variable']
            ]
        ];
    }

    /**
     * @dataProvider provideValidLinks
     */
    public function test_it_can_create_links($path, $expected, $params)
    {
        $this->assertEquals($expected, Link::to(RouteBuilder::get($path)->build(),$params));
    }

    public function test_it_fails_with_invalid_parameters()
    {
        $this->expectExceptionMessage('"var" is required to build this link.');

        Link::to(RouteBuilder::get('/{var}')->build(), ['another' => 'value']);
    }

    public function test_it_fails_with_invalid_wildcard_parameters()
    {
        $this->expectExceptionMessage('"var" must be an array to build this link.');

        Link::to(RouteBuilder::get('/{*var}')->build(), ['var' => 'value']);
    }

    public function test_it_fails_with_mismatching_parameters()
    {
        $this->expectExceptionMessage('"var" must not be an array to build this link.');

        Link::to(RouteBuilder::get('/{var}')->build(), ['var' => ['array', 'values']]);
    }
}
