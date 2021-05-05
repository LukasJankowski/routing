<?php

namespace Utilities;

use LukasJankowski\Routing\Utilities\Path;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    use PHPMock;

    public function providePaths(): array
    {
        return [
            [
                'given' => '',
                'expected' => '/'
            ],
            [
                'given' => '//?#',
                'expected' => '/'
            ],
            [
                'given' => 'nested/path',
                'expected' => '/nested/path'
            ],
            [
                'given' => 'path/',
                'expected' => '/path'
            ],
            [
                'given' => 'path/?',
                'expected' => '/path'
            ],
        ];
    }

    /**
     * @dataProvider providePaths
     */
    public function test_it_normalizes_paths($given, $expected)
    {
        $this->assertEquals($expected, Path::normalize($given));
    }

    public function provideSplits(): array
    {
        return [
            [
                'given' => '/segment/two',
                'expected' => ['segment', 'two']
            ],
            [
                'given' => '/',
                'expected' => []
            ],
            [
                'given' => '/segment',
                'expected' => ['segment']
            ],
        ];
    }

    /**
     * @dataProvider provideSplits
     */
    public function test_it_splits_paths($given, $expected)
    {
        $this->assertEquals($expected, Path::split($given));
    }

    public function test_it_splits_limited()
    {
        $this->assertEquals(['segment', 'two/three'], Path::split('/segment/two/three', 2));
    }

    public function provideDynamicSegments(): array
    {
        return [
            [
                'given' => '/{var}/static/{?opt:\d+}',
                'expected' => [
                    '{var}' => ['name' => 'var', 'pattern' => null],
                    '{?opt:\d+}' => ['name' => 'opt', 'pattern' => '\d+']
                ]
            ],
            [
                'given' => '/static/only',
                'expected' => [],
            ],
            [
                'given' => '/',
                'expected' => [],
            ],
            [
                'given' => '',
                'expected' => [],
            ],
        ];
    }

    /**
     * @dataProvider provideDynamicSegments
     */
    public function test_it_extracts_dynamic_segments_from_path($given, $expected)
    {
        $this->assertEquals($expected, Path::extractDynamicSegments($given));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_it_returns_an_empty_array_on_failed_match()
    {
        $this->getFunctionMock('LukasJankowski\Routing\Utilities', 'preg_match_all')
            ->expects($this->once())
            ->willReturnCallback(function ($regex, $path, &$matches) {
                $matches = [];

                return null;
            });

        $this->assertEquals([], Path::extractDynamicSegments('/some/path'));
    }

    public function provideOptionalSegments(): array
    {
        return [
            [
                'given' => '{?var}',
                'expected' => true,
            ],
            [
                'given' => '{*?var}',
                'expected' => true,
            ],
            [
                'given' => '{?*var}',
                'expected' => true,
            ],
            [
                'given' => '{var}',
                'expected' => false,
            ],
            [
                'given' => '{*var}',
                'expected' => false,
            ],
            [
                'given' => '/static',
                'expected' => false,
            ],
            [
                'given' => '/',
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider provideOptionalSegments
     */
    public function test_it_checks_if_the_segment_is_optional($given, $expected)
    {
        $this->assertEquals($expected, Path::isOptionalSegment($given));
    }

    public function provideWildcardSegments(): array
    {
        return [
            [
                'given' => '{*var}',
                'expected' => true,
            ],
            [
                'given' => '{*?var}',
                'expected' => true,
            ],
            [
                'given' => '{?*var}',
                'expected' => true,
            ],
            [
                'given' => '{var}',
                'expected' => false,
            ],
            [
                'given' => '{?var}',
                'expected' => false,
            ],
            [
                'given' => '/static',
                'expected' => false,
            ],
            [
                'given' => '/',
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider provideWildcardSegments
     */
    public function test_it_checks_if_the_segment_a_wildcard($given, $expected)
    {
        $this->assertEquals($expected, Path::isWildcardSegment($given));
    }
}
