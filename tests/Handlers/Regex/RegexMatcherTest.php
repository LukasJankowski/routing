<?php

namespace Handlers\Regex;

use LukasJankowski\Routing\Collection;
use LukasJankowski\Routing\Handlers\Regex\RegexMatcher;
use LukasJankowski\Routing\Handlers\Regex\RegexParser;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RegexMatcherTest extends TestCase
{
    private RegexMatcher $matcher;

    private RegexParser $parser;

    protected function setUp(): void
    {
        $this->matcher = new RegexMatcher();
        $this->parser = new RegexParser();
    }

    public function provideValidRoutes(): array
    {
        return [
            ['given' => '/', 'expected' => '/'],
            ['given' => '/path', 'expected' => '/path'],
            ['given' => '/{var}', 'expected' => '/anything'],
            ['given' => '/nested/{var}', 'expected' => '/nested/anything'],
            ['given' => '/{var}/nested', 'expected' => '/anything/nested'],
            ['given' => '/{double}/{var}', 'expected' => '/anything/nested'],
            ['given' => '/in/{between}/nested', 'expected' => '/in/anything/nested'],
            ['given' => '/{var:\d+}', 'expected' => '/123123'],
            ['given' => '/{var:\d{4}}', 'expected' => '/1234'],
            ['given' => '/{var:[a-z]+}', 'expected' => '/abcdefg'],
            ['given' => '/nested/{var:\d+}', 'expected' => '/nested/123789'],
            ['given' => '/{?var}', 'expected' => '/'],
            ['given' => '/{?var:\d+}', 'expected' => '/'],
            ['given' => '/static/{?var}', 'expected' => '/static'],
            ['given' => '/static/{?var:\d+}', 'expected' => '/static'],
            ['given' => '/{?var}/static', 'expected' => '/static'],
            ['given' => '/{?var}/{?test}', 'expected' => '/'],
            ['given' => '/in/{?var}/between', 'expected' => '/in/between'],
            ['given' => '/{*var}', 'expected' => '/anything/more/test'],
            ['given' => '/{*ignored:\d+}', 'expected' => '/anything/more/test'],
            ['given' => '/static/{*var}', 'expected' => '/static/anything/more/test'],
            ['given' => '/{*early}/static', 'expected' => '/anything/more/test/static'],
            ['given' => '/in/{*var}/between', 'expected' => '/in/anything/more/test/between'],
            ['given' => '/{*two}/{*wildcards}', 'expected' => '/anything/more/test/another'],
            ['given' => '/{*?var}', 'expected' => '/'],
            ['given' => '/{?*var}', 'expected' => '/can/be/as/long/as/possible'],
            ['given' => '/static/{?*var}', 'expected' => '/static'],
            ['given' => '/{?*var}/static', 'expected' => '/static'],
            ['given' => '/combo/{var}/{?opt}/{*wildcard}', 'expected' => '/combo/anything/with/patterns'],
            ['given' => '/combo/{?opt}/{*wildcard}/{var}', 'expected' => '/combo/with/patterns/anything'],
            ['given' => '/combo/{?opt}/{?*wildcard}/{?var}', 'expected' => '/combo'],
            ['given' => '/combo/{?*wc}/static/{var}/{?opt}', 'expected' => '/combo/static/anything'],
        ];
    }

    /**
     * @dataProvider provideValidRoutes
     */
    public function test_it_can_match_valid_routes($given, $expected)
    {
        $route = RouteBuilder::get($given)->build();
        $request = new Request('get', $expected, '', '');
        $route = $this->parser->parse([$route])[0];

        $this->assertTrue($this->matcher->matches($route, $request));
    }

    public function provideInvalidRoutes(): array
    {
        return [
            ['given' => '/', 'expected' => '/asd'],
            ['given' => '/path', 'expected' => '/p4th'],
            ['given' => '/{var}', 'expected' => '/too/long'],
            ['given' => '/nested/{var}', 'expected' => '/nested'],
            ['given' => '/{var}/nested', 'expected' => '/'],
            ['given' => '/{double}/{var}', 'expected' => '/anything/nested/too/long'],
            ['given' => '/in/{between}/nested', 'expected' => '/in/nested'],
            ['given' => '/{var:\d+}', 'expected' => '/abc'],
            ['given' => '/{var:\d{4}}', 'expected' => '/12345'],
            ['given' => '/{var:[a-z]+}', 'expected' => '/ABCDEF'],
            ['given' => '/nested/{var:\d+}', 'expected' => '/nested/abcdef'],
            ['given' => '/{?var}', 'expected' => '/optional/too/long'],
            ['given' => '/{?var:\d+}', 'expected' => '/abc/too/long'],
            ['given' => '/static/{?var}', 'expected' => '/static/too/long'],
            ['given' => '/static/{?var:\d+}', 'expected' => '/static/abcdef'],
            ['given' => '/{?var}/static', 'expected' => '/'],
            ['given' => '/{?var}/{?test}', 'expected' => '/too/long/with/segments'],
            ['given' => '/in/{?var}/between', 'expected' => '/in'],
            ['given' => '/{*var}', 'expected' => '/'],
            ['given' => '/{*ignored:\d+}', 'expected' => '/'],
            ['given' => '/static/{*var}', 'expected' => '/static'],
            ['given' => '/{*early}/static', 'expected' => '/wild/card/invalid'],
            ['given' => '/in/{*var}/between', 'expected' => '/in/between'],
            ['given' => '/{*two}/{*wildcards}', 'expected' => '/anything'],
            ['given' => '/static/{?*var}', 'expected' => '/'],
            ['given' => '/{?*var}/static', 'expected' => '/'],
            ['given' => '/combo/{var}/{?opt}/{*wildcard}', 'expected' => '/combo/with'],
            ['given' => '/combo/{?opt}/{*wildcard}/{var}', 'expected' => '/combo/optional'],
            ['given' => '/combo/{?opt}/{?*wildcard}/{?var}', 'expected' => '/'],
            ['given' => '/combo/{?*wc}/static/{var}/{?opt}', 'expected' => '/combo/static/var/too/long'],
        ];
    }

    /**
     * @dataProvider provideInvalidRoutes
     */
    public function test_it_cant_match_invalid_routes($given, $expected)
    {
        $route = RouteBuilder::get($given)->build();
        $request = new Request('get', $expected, '', '');
        $route = $this->parser->parse([$route])[0];

        $this->assertFalse($this->matcher->matches($route, $request));
    }

    public function test_it_throws_exception_on_bad_method()
    {
        $route = RouteBuilder::get('/')->build();
        $request = new Request('post', '/', '', '');
        $route = $this->parser->parse([$route])[0];

        $this->expectExceptionMessage('constraint.method.mismatch');

        $this->matcher->matches($route, $request);
    }

    public function test_it_throws_exception_on_bad_host()
    {
        $route = RouteBuilder::get('/')->host('another.com')->build();
        $request = new Request('get', '/', 'test.com', '');
        $route = $this->parser->parse([$route])[0];

        $this->expectExceptionMessage('constraint.host.mismatch');

        $this->matcher->matches($route, $request);

    }

    public function test_it_throws_exception_on_bad_scheme()
    {
        $route = RouteBuilder::get('/')->scheme('https')->build();
        $request = new Request('get', '/', '', 'http');
        $request->scheme = 'HTTP';
        $route = $this->parser->parse([$route])[0];

        $this->expectExceptionMessage('constraint.scheme.mismatch');

        $this->matcher->matches($route, $request);
    }

    public function provideValidConstraints(): array
    {
        return [
            [
                'given' => '/{var}',
                'path' => '/anything',
                'expected' => ['var' => 'anything']
            ],
            [
                'given' => '/{var:\d+}',
                'path' => '/123123',
                'expected' => ['var' => '123123']
            ],
            [
                'given' => '/{?var}',
                'path' => '/anything',
                'expected' => ['var' => 'anything']
            ],
            [
                'given' => '/{?var}/{?test}',
                'path' => '/123',
                'expected' => ['var' => '123', 'test' => null]
            ],
            [
                'given' => '/{*var}',
                'path' => '/123/456/abc',
                'expected' => ['var' => ['123', '456', 'abc']]
            ],
            [
                'given' => '/{*var}/static',
                'path' => '/123/456/static',
                'expected' => ['var' => ['123', '456']]
            ],
            [
                'given' => '/{*var}/{*test}',
                'path' => '/123/456/abc',
                'expected' => ['var' => ['123', '456'], 'test' => ['abc']]
            ],
            [
                'given' => '/{*?var}',
                'path' => '/',
                'expected' => ['var' => null]
            ],
            [
                'given' => '/combo/{var}/{?opt}/{*wildcard}',
                'path' => '/combo/123/wild',
                'expected' => ['var' => '123', 'opt' => null, 'wildcard' => ['wild']]
            ],
            [
                'given' => '/combo/{?*wc}/static/{var}/{?opt}',
                'path' => '/combo/static/anything',
                'expected' => ['wc' => null, 'var' => 'anything', 'opt' => null]
            ]
        ];
    }

    /**
     * @dataProvider provideValidConstraints
     */
    public function test_it_can_match_segment_constraints($given, $path, $expected)
    {
        $route = RouteBuilder::get($given)->build();
        $request = new Request('get', $path, '', '');
        $route = $this->parser->parse([$route])[0];

        $this->assertTrue($this->matcher->matches($route, $request));
        $this->assertEquals($expected, $route->getParameters());
    }

    public function provideDefaults(): array
    {
        return [
            [
                'route' => '/{?var}',
                'path' => '/',
                'defaults' => ['var' => '123'],
                'expected' => ['var' => '123']
            ],
            [
                'route' => '/{?var}/{?test}',
                'path' => '/123',
                'defaults' => ['test' => '456'],
                'expected' => ['var' => '123', 'test' => '456']
            ],
            [
                'route' => '/{*?var}',
                'path' => '/',
                'defaults' => ['var' => ['123', '456']],
                'expected' => ['var' => ['123', '456']]
            ],
            [
                'route' => '/combo/{var}/{?opt}/{*wildcard}',
                'path' => '/combo/123/wild',
                'defaults' => ['opt' => 'optional'],
                'expected' => ['var' => '123', 'opt' => 'optional', 'wildcard' => ['wild']]
            ],
            [
                'route' => '/combo/{?*wc}/static/{var}/{?opt}',
                'path' => '/combo/static/anything',
                'defaults' => ['wc' => ['123', '456'], 'opt' => 'abc'],
                'expected' => ['wc' => ['123', '456'], 'var' => 'anything', 'opt' => 'abc']
            ]
        ];
    }

    /**
     * @dataProvider provideDefaults
     */
    public function test_it_can_set_defaults_on_segments($route, $path, $defaults, $expected)
    {
        $route = RouteBuilder::get($route)->default($defaults)->build();
        $request = new Request('get', $path, '', '');
        $route = $this->parser->parse([$route])[0];

        $this->assertTrue($this->matcher->matches($route, $request));
        $this->assertEquals($expected, $route->getParameters());
    }

    public function test_it_can_match_a_complex_route()
    {
        $route = RouteBuilder::match(['get', 'post'], '/{?opt}/{var}/{*wc}')
            ->host('api.host.com')
            ->scheme('http')
            ->constraint(['var' => '\d+'])
            ->default(['opt' => 'optional'])
            ->build();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/123456/wild/cards';
        $_SERVER['SERVER_NAME'] = 'api.host.com';
        $_SERVER['REQUEST_SCHEME'] = 'http';

        $request = Request::fromSuperGlobal();

        $route = $this->parser->parse([$route])[0];

        $this->assertTrue($this->matcher->matches($route, $request));
        $this->assertEquals(
            ['var' => '123456', 'opt' => 'optional', 'wc' => ['wild', 'cards']],
            $route->getParameters()
        );
    }

    public function test_it_throws_an_exception_on_invalid_constraint()
    {
        $route = RouteBuilder::get('/')
            ->constraint(Collection::class, 'some-value')
            ->build();

        $request = new Request('get', '/', '', '');
        $route = $this->parser->parse([$route])[0];

        $this->expectException(RuntimeException::class);

        $this->matcher->matches($route, $request);
    }
}
