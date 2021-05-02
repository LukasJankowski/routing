<?php

namespace Handlers\Fixed;

use LukasJankowski\Routing\Collection;
use LukasJankowski\Routing\Handlers\Fixed\FixedMatcher;
use LukasJankowski\Routing\Handlers\MatcherInterface;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FixedMatcherTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $matcher = new FixedMatcher();

        $this->assertInstanceOf(MatcherInterface::class, $matcher);
        $this->assertInstanceOf(FixedMatcher::class, $matcher);
    }

    public function test_it_can_match_routes()
    {
        $matcher = new FixedMatcher();

        $valid = [
            '/',
            '/path',
            '/nested/path',
            '/deeply/nested/path/with/many/segments'
        ];

        $invalid = [
            '/' => '/path',
            '/path' => '/p4th',
            '/nested/path' => '/mismatch/path',
            '/deeply/nested/path' => '/short',
            '/inverse' => '/deeply/nested/path'
        ];

        foreach ($valid as $path) {
            $route = RouteBuilder::get($path)->build();
            $request = new Request('get', $path, '', '');

            $this->assertTrue($matcher->matches($route, $request));
        }

        foreach ($invalid as $routePath => $requestPath) {
            $route = RouteBuilder::get($routePath)->build();
            $request = new Request('get', $requestPath, '', '');

            $this->assertFalse($matcher->matches($route, $request));
        }
    }

    public function test_it_throws_exception_on_bad_method()
    {
        $matcher = new FixedMatcher();

        $route = RouteBuilder::get('/')->build();
        $request = new Request('post', '/', '', '');

        $this->expectExceptionMessage('constraint.method.mismatch');

        $matcher->matches($route, $request);
    }

    public function test_it_throws_exception_on_bad_host()
    {
        $matcher = new FixedMatcher();

        $route = RouteBuilder::get('/')->host('another.com')->build();
        $request = new Request('get', '/', 'test.com', '');

        $this->expectExceptionMessage('constraint.host.mismatch');

        $matcher->matches($route, $request);

    }

    public function test_it_throws_exception_on_bad_scheme()
    {
        $matcher = new FixedMatcher();

        $route = RouteBuilder::get('/')->scheme('https')->build();
        $request = new Request('get', '/', '', 'http');
        $request->scheme = 'HTTP';

        $this->expectExceptionMessage('constraint.scheme.mismatch');

        $matcher->matches($route, $request);
    }

    public function test_it_throws_an_exception_on_invalid_constraint()
    {
        $route = RouteBuilder::get('/')
            ->constraint(Collection::class, 'some-value')
            ->build();

        $matcher = new FixedMatcher();

        $request = new Request('get', '/', '', '');

        $this->expectException(RuntimeException::class);

        $matcher->matches($route, $request);
    }
}
