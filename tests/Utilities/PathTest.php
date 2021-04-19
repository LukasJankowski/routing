<?php

namespace Utilities;

use LukasJankowski\Routing\Utilities\Path;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function test_it_normalizes_paths()
    {
        $this->assertEquals('/', Path::normalize(''));
        $this->assertEquals('/', Path::normalize('//?#'));
    }

    public function test_it_splits_paths()
    {
        $this->assertEquals(['segment', 'two'], Path::split('/segment/two'));
        $this->assertEquals([], Path::split('/'));
        $this->assertEquals(['segment'], Path::split('/segment'));

        $this->assertEquals(['segment', 'two/three'], Path::split('/segment/two/three', 2));
    }

    public function test_it_extracts_dynamic_segments_from_path()
    {
        $this->assertEquals(
            [
                '{var}' => ['name' => 'var', 'pattern' => null],
                '{?opt:\d+}' => ['name' => 'opt', 'pattern' => '\d+']
            ],
            Path::extractDynamicSegments('/{var}/static/{?opt:\d+}')
        );

        $this->assertEquals([], Path::extractDynamicSegments('/static/only'));
        $this->assertEquals([], Path::extractDynamicSegments('/'));
    }

    public function test_it_checks_if_the_segment_is_optional()
    {
        $this->assertTrue(Path::isOptionalSegment('{?var}'));
        $this->assertTrue(Path::isOptionalSegment('{*?var}'));
        $this->assertTrue(Path::isOptionalSegment('{?*var}'));

        $this->assertFalse(Path::isOptionalSegment('{var}'));
        $this->assertFalse(Path::isOptionalSegment('{*var}'));
        $this->assertFalse(Path::isOptionalSegment('/static'));
        $this->assertFalse(Path::isOptionalSegment('/s'));
        $this->assertFalse(Path::isOptionalSegment('/'));
    }

    public function test_it_checks_if_the_segment_a_wildcard()
    {
        $this->assertTrue(Path::isWildcardSegment('{*var}'));
        $this->assertTrue(Path::isWildcardSegment('{*?var}'));
        $this->assertTrue(Path::isWildcardSegment('{?*var}'));

        $this->assertFalse(Path::isWildcardSegment('{var}'));
        $this->assertFalse(Path::isWildcardSegment('{?var}'));
        $this->assertFalse(Path::isWildcardSegment('/static'));
        $this->assertFalse(Path::isWildcardSegment('/s'));
        $this->assertFalse(Path::isWildcardSegment('/'));
    }
}
