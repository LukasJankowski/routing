<?php

namespace Matchers;

use LukasJankowski\Routing\CompiledRouteCollection;
use LukasJankowski\Routing\Matchers\RouteMatcherInterface;
use LukasJankowski\Routing\Matchers\RegexRouteMatcher;
use LukasJankowski\Routing\Parser\RegexRouteParser;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RegexRouteMatcherTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $matcher = new RegexRouteMatcher();

        $this->assertInstanceOf(RouteMatcherInterface::class, $matcher);
        $this->assertInstanceOf(RegexRouteMatcher::class, $matcher);
    }

    public function test_it_can_match_routes()
    {
        $matcher = new RegexRouteMatcher();
        $parser = new RegexRouteParser();

        $valid = [
            '/' => '/',
            '/path' => '/path',
            '/{var}' => '/anything',
            '/nested/{var}' => '/nested/anything',
            '/{var}/nested' => '/anything/nested',
            '/{double}/{var}' => '/anything/nested',
            '/in/{between}/nested' => '/in/anything/nested',

            '/{var:\d+}' => '/123123',
            '/{var:\d{4}}' => '/1234',
            '/{var:[a-z]+}' => '/abcdefg',
            '/nested/{var:\d+}' => '/nested/123789',

            '/{?var}' => '/',
            '/{?var:\d+}' => '/',
            '/static/{?var}' => '/static',
            '/static/{?var:\d+}' => '/static',
            '/{?var}/static' => '/static',
            '/{?var}/{?test}' => '/',
            '/in/{?var}/between' => '/in/between',

            '/{*var}' => '/anything/more/test',
            '/{*ignored:\d+}' => '/anything/more/test',
            '/static/{*var}' => '/static/anything/more/test',
            '/{*early}/static' => '/anything/more/test/static',
            '/in/{*var}/between' => '/in/anything/more/test/between',
            '/{*two}/{*wildcards}' => '/anything/more/test/another',

            '/{*?var}' => '/',
            '/{?*var}' => '/can/be/as/long/as/possible',
            '/static/{?*var}' => '/static',
            '/{?*var}/static' => '/static',

            '/combo/{var}/{?opt}/{*wildcard}' => '/combo/anything/with/patterns',
            '/combo/{?opt}/{*wildcard}/{var}' => '/combo/with/patterns/anything',
            '/combo/{?opt}/{?*wildcard}/{?var}' => '/combo',
            '/combo/{?*wc}/static/{var}/{?opt}' => '/combo/static/anything',
        ];

        $invalid = [
            '/' => '/asd',
            '/path' => '/p4th',
            '/{var}' => '/too/long',
            '/nested/{var}' => '/nested',
            '/{var}/nested' => '/',
            '/{double}/{var}' => '/anything/nested/too/long',
            '/in/{between}/nested' => '/in/nested',

            '/{var:\d+}' => '/abc',
            '/{var:\d{4}}' => '/12345',
            '/{var:[a-z]+}' => '/ABCDEF',
            '/nested/{var:\d+}' => '/nested/abcdef',

            '/{?var}' => '/optional/too/long',
            '/{?var:\d+}' => '/abc/too/long',
            '/static/{?var}' => '/static/too/long',
            '/static/{?var:\d+}' => '/static/abcdef',
            '/{?var}/static' => '/',
            '/{?var}/{?test}' => '/too/long/with/segments',
            '/in/{?var}/between' => '/in',

            '/{*var}' => '/',
            '/{*ignored:\d+}' => '/',
            '/static/{*var}' => '/static',
            '/{*early}/static' => '/wild/card/invalid',
            '/in/{*var}/between' => '/in/between',
            '/{*two}/{*wildcards}' => '/anything',

            //'/{*?var}' => '/', //always true
            //'/{?*var}' => '/', //always true
            '/static/{?*var}' => '/',
            '/{?*var}/static' => '/',

            '/combo/{var}/{?opt}/{*wildcard}' => '/combo/with',
            '/combo/{?opt}/{*wildcard}/{var}' => '/combo/optional',
            '/combo/{?opt}/{?*wildcard}/{?var}' => '/',
            '/combo/{?*wc}/static/{var}/{?opt}' => '/combo/static/var/too/long',
        ];

        foreach ($valid as $routePath => $requestPath) {
            $route = RouteBuilder::get($routePath)->build();
            $request = new Request('get', $requestPath, '', '');
            $route = $parser->parse([$route])[0];

            $this->assertTrue($matcher->matches($route, $request));
        }

        foreach ($invalid as $routePath => $requestPath) {
            $route = RouteBuilder::get($routePath)->build();
            $request = new Request('get', $requestPath, '', '');
            $route = $parser->parse([$route])[0];

            $this->assertFalse($matcher->matches($route, $request));
        }
    }

    public function test_it_throws_exception_on_bad_method()
    {
        $matcher = new RegexRouteMatcher();
        $parser = new RegexRouteParser();

        $route = RouteBuilder::get('/')->build();
        $request = new Request('post', '/', '', '');
        $route = $parser->parse([$route])[0];

        $this->expectExceptionMessage('constraint.method.mismatch');

        $matcher->matches($route, $request);
    }

    public function test_it_throws_exception_on_bad_host()
    {
        $matcher = new RegexRouteMatcher();
        $parser = new RegexRouteParser();

        $route = RouteBuilder::get('/')->host('another.com')->build();
        $request = new Request('get', '/', 'test.com', '');
        $route = $parser->parse([$route])[0];

        $this->expectExceptionMessage('constraint.host.mismatch');

        $matcher->matches($route, $request);

    }

    public function test_it_throws_exception_on_bad_scheme()
    {
        $matcher = new RegexRouteMatcher();
        $parser = new RegexRouteParser();

        $route = RouteBuilder::get('/')->scheme('https')->build();
        $request = new Request('get', '/', '', 'http');
        $request->scheme = 'HTTP';
        $route = $parser->parse([$route])[0];

        $this->expectExceptionMessage('constraint.scheme.mismatch');

        $matcher->matches($route, $request);
    }

    public function test_it_can_match_segment_constraints()
    {
        $matcher = new RegexRouteMatcher();
        $parser = new RegexRouteParser();

        $valid = [
            '/{var}' => ['path' => '/anything', 'expect' => ['var' => 'anything']],
            '/{var:\d+}' => ['path' => '/123123', 'expect' => ['var' => '123123']],
            '/{?var}' => ['path' => '/anything', 'expect' => ['var' => 'anything']],
            '/{?var}/{?test}' => ['path' => '/123', 'expect' => ['var' => '123', 'test' => null]],
            '/{*var}' => ['path' => '/123/456/abc', 'expect' => ['var' => ['123', '456', 'abc']]],
            '/{*var}/static' => ['path' => '/123/456/static', 'expect' => ['var' => ['123', '456']]],
            '/{*var}/{*test}' => ['path' => '/123/456/abc', 'expect' => ['var' => ['123', '456'], 'test' => ['abc']]],
            '/{*?var}' => ['path' => '/', 'expect' => ['var' => null]],
            '/combo/{var}/{?opt}/{*wildcard}' => [
                'path' => '/combo/123/wild',
                'expect' => ['var' => '123', 'opt' => null, 'wildcard' => ['wild']]
            ],
            '/combo/{?*wc}/static/{var}/{?opt}' => [
                'path' => '/combo/static/anything',
                'expect' => ['wc' => null, 'var' => 'anything', 'opt' => null]
            ]
        ];

        $defaults = [
            '/{?var}' => [
                'path' => '/',
                'defaults' => ['var' => '123'],
                'expect' => ['var' => '123']
            ],
            '/{?var}/{?test}' => [
                'path' => '/123',
                'defaults' => ['test' => '456'],
                'expect' => ['var' => '123', 'test' => '456']
            ],
            '/{*?var}' => [
                'path' => '/',
                'defaults' => ['var' => ['123', '456']],
                'expect' => ['var' => ['123', '456']]
            ],
            '/combo/{var}/{?opt}/{*wildcard}' => [
                'path' => '/combo/123/wild',
                'defaults' => ['opt' => 'optional'],
                'expect' => ['var' => '123', 'opt' => 'optional', 'wildcard' => ['wild']]
            ],
            '/combo/{?*wc}/static/{var}/{?opt}' => [
                'path' => '/combo/static/anything',
                'defaults' => ['wc' => ['123', '456'], 'opt' => 'abc'],
                'expect' => ['wc' => ['123', '456'], 'var' => 'anything', 'opt' => 'abc']
            ]
        ];

        foreach ($valid as $routePath => $props) {
            $route = RouteBuilder::get($routePath)->build();
            $request = new Request('get', $props['path'], '', '');
            $route = $parser->parse([$route])[0];

            $this->assertTrue($matcher->matches($route, $request));
            $this->assertEquals($props['expect'], $route->parsedParameters);
        }

        foreach ($defaults as $routePath => $props) {
            $route = RouteBuilder::get($routePath)->default($props['defaults'])->build();
            $request = new Request('get', $props['path'], '', '');
            $route = $parser->parse([$route])[0];

            $this->assertTrue($matcher->matches($route, $request));
            $this->assertEquals($props['expect'], $route->parsedParameters);
        }
    }

    public function test_it_can_match_a_complex_route()
    {
        $matcher = new RegexRouteMatcher();
        $parser = new RegexRouteParser();

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

        $route = $parser->parse([$route])[0];

        $this->assertTrue($matcher->matches($route, $request));
        $this->assertEquals(
            ['var' => '123456', 'opt' => 'optional', 'wc' => ['wild', 'cards']],
            $route->parsedParameters
        );
    }

    public function test_it_throws_an_exception_on_invalid_constraint()
    {
        $route = RouteBuilder::get('/')
            ->constraint(CompiledRouteCollection::class, 'some-value')
            ->build();

        $matcher = new RegexRouteMatcher();
        $parser = new RegexRouteParser();

        $request = new Request('get', '/', '', '');
        $route = $parser->parse([$route])[0];

        $this->expectException(RuntimeException::class);

        $matcher->matches($route, $request);
    }
}
