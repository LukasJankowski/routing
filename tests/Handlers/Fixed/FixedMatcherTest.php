<?php

namespace Handlers\Fixed;

use LukasJankowski\Routing\Collection;
use LukasJankowski\Routing\Handlers\Fixed\FixedMatcher;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FixedMatcherTest extends TestCase
{
    private FixedMatcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new FixedMatcher();
    }

    public function provideValidPaths(): array
    {
        return [
            ['/'],
            ['/path'],
            ['/nested/path'],
            ['/deeply/nested/path/with/many/segments'],
        ];
    }

    /**
     * @dataProvider provideValidPaths
     */
    public function test_it_can_match_routes($given)
    {
        $route = RouteBuilder::get($given)->build();
        $request = new Request('get', $given, '', '');

        $this->assertTrue($this->matcher->matches($route, $request));
    }

    public function provideInvalidPaths(): array
    {
        return [
            ['route' => '/', 'request' => '/path'],
            ['route' => '/path', 'request' => '/p4th'],
            ['route' => '/nested/path', 'request' => '/mismatch/path'],
            ['route' => '/deeply/nested/path', 'request' => '/short'],
            ['route' => '/inverse', 'request' => '/deeply/nested/path'],
        ];
    }

    /**
     * @dataProvider provideInvalidPaths
     */
    public function test_it_cant_match_invalid_routes($route, $request)
    {
        $route = RouteBuilder::get($route)->build();
        $request = new Request('get', $request, '', '');

        $this->assertFalse($this->matcher->matches($route, $request));
    }

    public function test_it_throws_exception_on_bad_method()
    {
        $route = RouteBuilder::get('/')->build();
        $request = new Request('post', '/', '', '');

        $this->expectExceptionMessage('constraint.method.mismatch');

        $this->matcher->matches($route, $request);
    }

    public function test_it_throws_exception_on_bad_host()
    {
        $route = RouteBuilder::get('/')->host('another.com')->build();
        $request = new Request('get', '/', 'test.com', '');

        $this->expectExceptionMessage('constraint.host.mismatch');

        $this->matcher->matches($route, $request);
    }

    public function test_it_throws_exception_on_bad_scheme()
    {
        $route = RouteBuilder::get('/')->scheme('https')->build();
        $request = new Request('get', '/', '', 'http');
        $request->scheme = 'HTTP';

        $this->expectExceptionMessage('constraint.scheme.mismatch');

        $this->matcher->matches($route, $request);
    }

    public function test_it_throws_an_exception_on_invalid_constraint()
    {
        $route = RouteBuilder::get('/')
            ->constraint(Collection::class, 'some-value')
            ->build();

        $request = new Request('get', '/', '', '');

        $this->expectException(RuntimeException::class);

        $this->matcher->matches($route, $request);
    }
}
