<?php

namespace Loaders\Php;

use ErrorException;
use LukasJankowski\Routing\Loaders\Php\PhpCache;
use LukasJankowski\Routing\RouteBuilder;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

class PhpCacheTest extends TestCase
{
    use PHPMock;

    public function test_it_throws_an_exception_when_failing_to_write()
    {
        $this->getFunctionMock('LukasJankowski\Routing\Loaders\Php', 'file_put_contents')
            ->expects($this->once())
            ->willReturn(false);

        $this->expectException(ErrorException::class);

        $cache = new PhpCache(__DIR__ . '/../../fixtures/doesnt_exist.cache');

        $cache->set([]);
    }

    public function test_it_can_read_and_write_to_file()
    {
        $cache = new PhpCache(__DIR__ . '/../../fixtures/php_route_cache.cache');

        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::match(['post', 'put'], '/route')->build(),
            RouteBuilder::get('/test1')->build(),
            RouteBuilder::get('/test2')->build(),
        ];

        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $cache = new PhpCache(__DIR__ . '/../../fixtures/doesnt_exist.cache');
        $this->assertEquals([], $cache->get());

        $cache = new PhpCache(__DIR__ . '/../../fixtures/php_cache_invalid.cache');
        $this->assertEquals([], $cache->get());
    }
}
