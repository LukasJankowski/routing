<?php

namespace Loaders\Yaml;

use Composer\InstalledVersions;
use ErrorException;
use Exception;
use InvalidArgumentException;
use LukasJankowski\Routing\Loaders\Yaml\YamlResource;
use LukasJankowski\Routing\RouteBuilder;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class YamlResourceTest extends TestCase
{
    public function test_it_throws_an_exception_if_package_not_installed()
    {
        $this->expectException(RuntimeException::class);

        $this->mockInstalledVersions(false);

        new YamlResource('some-file');
    }

    private function mockInstalledVersions(bool $return = true): void
    {
        $mock = Mockery::mock('overload:' . InstalledVersions::class);
        $mock->shouldReceive('isInstalled')
            ->andReturn($return);
    }

    public function test_it_throws_an_exception_on_invalid_file()
    {
        $this->mockInstalledVersions();

        $this->expectException(InvalidArgumentException::class);

        new YamlResource('not_found');
    }

    public function test_it_can_retrieve_the_routes_from_resource()
    {
        $this->mockInstalledVersions();

        $return = [
            '/' => ['method' => 'get'],
            '/path/{var}/{*?wc}' => [
                'method' => ['get', 'post'],
                'action' => 'ControllerClass@method',
                'name' => 'route.name',
                'constraint' => ['var' => '\d+'],
                'middleware' => ['test_middleware', 'test_middleware_2'],
                'default' => ['wc' => ['some', 'segments']],
            ],
            0 => [
                'path' => '/prefix',
                'name' => 'prefix.',
                'middleware' => 'prefix_middleware',
                'group' => [
                    '/grouped' => [
                        'method' => ['put', 'patch'],
                        'name' => 'grouped',
                        'middleware' => 'grouped_middleware',
                    ],
                    '/another' => [
                        'method' => 'get',
                    ],
                    0 => [
                        'path' => '/nested',
                        'group' => [
                            '/path' => [
                                'method' => 'get',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mock = Mockery::mock('alias:' . 'Symfony\Component\Yaml\Yaml');
        $mock->shouldReceive('parseFile')
            ->andReturn($return);

        $resource = new YamlResource(__DIR__ . '/../../fixtures/routes.yml');

        $this->assertEquals(
            [
                RouteBuilder::get('/')->build(),
                RouteBuilder::match(['get', 'post'], '/path/{var}/{*?wc}', 'ControllerClass@method')
                    ->name('route.name')
                    ->constraint('var', '\d+')
                    ->middleware(['test_middleware', 'test_middleware_2'])
                    ->default(['wc' => ['some', 'segments']])
                    ->build(),
                RouteBuilder::match(['put', 'patch'], '/prefix/grouped')
                    ->name('prefix.grouped')
                    ->middleware(['prefix_middleware', 'grouped_middleware'])
                    ->build(),
                RouteBuilder::get('/prefix/another')
                    ->name('prefix.')
                    ->middleware('prefix_middleware')
                    ->build(),
                RouteBuilder::get('/prefix/nested/path')
                    ->name('prefix.')
                    ->middleware('prefix_middleware')
                    ->build(),
            ],
            $resource->get()
        );
    }

    public function test_it_throws_an_exception_if_no_array_is_returned()
    {
        $this->mockInstalledVersions();

        $this->expectException(ErrorException::class);

        $mock = Mockery::mock('alias:' . 'Symfony\Component\Yaml\Yaml');
        $mock->shouldReceive('parseFile')
            ->andThrow(new Exception());

        $resource = new YamlResource(__DIR__ . '/../../fixtures/invalid_routes.yml');
        $resource->get();
    }
}
