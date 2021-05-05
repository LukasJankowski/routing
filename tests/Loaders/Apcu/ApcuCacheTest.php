<?php

namespace Loaders\Apcu;

use ErrorException;
use LukasJankowski\Routing\Loaders\Apcu\ApcuCache;
use LukasJankowski\Routing\RouteBuilder;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ApcuCacheTest extends TestCase
{
    use PHPMock;

    private const NS = 'LukasJankowski\Routing\Loaders\Apcu';

    private function mockFunction(string $name, mixed $return): void
    {
        $mock = $this->getFunctionMock(self::NS, $name);
        $mock->expects($this->once())->willReturn($return);
    }

    public function test_it_throws_an_exception_if_extension_is_not_loaded()
    {
        $this->mockFunction('extension_loaded', false);

        $this->expectException(RuntimeException::class);
        new ApcuCache('key');
    }

    public function test_it_throws_an_exception_if_extension_is_not_enabled()
    {
        $this->mockFunction('extension_loaded', true);
        $this->mockFunction('apcu_enabled', false);

        $this->expectException(RuntimeException::class);
        new ApcuCache('key');
    }

    public function test_it_returns_an_empty_array_if_no_cache_exists()
    {
        $this->mockFunction('extension_loaded', true);
        $this->mockFunction('apcu_enabled', true);
        $this->mockFunction('apcu_fetch', []);

        $cache = new ApcuCache('not-existent');
        $this->assertEquals([], $cache->get());
    }

    public function test_it_throws_an_exception_if_storing_fails()
    {
        $this->mockFunction('extension_loaded', true);
        $this->mockFunction('apcu_enabled', true);
        $this->mockFunction('apcu_store', false);

        $this->expectException(ErrorException::class);

        $cache = new ApcuCache('failed');
        $cache->set([]);
    }

    public function test_it_can_read_and_write_routes()
    {
        $routes = [
            RouteBuilder::get('/')->build(),
            RouteBuilder::match(['post', 'put'], '/route')->build(),
            RouteBuilder::get('/test1')->build(),
            RouteBuilder::get('/test2')->build(),
        ];

        $this->mockFunction('extension_loaded', true);
        $this->mockFunction('apcu_enabled', true);
        $this->mockFunction('apcu_store', true);

        $this->getFunctionMock(self::NS, 'apcu_fetch')
            ->expects($this->once())
            ->willReturnCallback(function ($key, &$success) use ($routes) {
                $success = true;

                return $routes;
            });

        $cache = new ApcuCache('existing');
        $cache->set($routes);

        $this->assertEquals($routes, $cache->get());
    }
}
