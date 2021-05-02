<?php

use LukasJankowski\Routing\Link;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public function test_it_can_create_links()
    {
        $valid = [
            '/' => ['expected' => '/', 'params' => []],
            '/path' => ['expected' => '/path', 'params' => []],
            '/{var}' => ['expected' => '/anything', 'params' => ['var' => 'anything']],
            '/nested/{var}' => ['expected' => '/nested/anything', 'params' => ['var' => 'anything']],
            '/{var}/nested' => ['expected' => '/anything/nested', 'params' => ['var' => 'anything']],
            '/{double}/{var}' => [
                'expected' => '/anything/nested',
                'params' => ['double' => 'anything', 'var' => 'nested']
            ],
            '/in/{between}/nested' => ['expected' => '/in/anything/nested', 'params' => ['between' => 'anything']],

            '/{var:\d+}' => ['expected' => '/123123', 'params' => ['var' => '123123']],

            '/{?var}' => ['expected' => '/', 'params' => []],
            '/{?var}/static' => ['expected' => '/static', 'params' => []],
            '/{?var}/{?test}' => ['expected' => '/', 'params' => []],

            '/{*var}' => ['expected' => '/anything/more/test', 'params' => ['var' => ['anything', 'more', 'test']]],
            '/static/{*var}' => [
                'expected' => '/static/anything/more/test',
                'params' => ['var' => ['anything', 'more', 'test']]
            ],

            '/{*?var}' => ['expected' => '/', 'params' => []],

            '/long/static/part/{*wc}/{var}' => [
                'expected' => '/long/static/part/wildcard/segments/variable',
                'params' => ['wc' => ['wildcard', 'segments'], 'var' => 'variable']
            ]
        ];

        foreach ($valid as $route => $expected) {

            $route = RouteBuilder::get($route)->build();

            $this->assertEquals($expected['expected'], Link::to($route, $expected['params']));
        }
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
