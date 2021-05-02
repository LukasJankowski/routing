<?php

use LukasJankowski\Routing\PatternRegistry;
use PHPUnit\Framework\TestCase;

class PatternRegistryTest extends TestCase
{
    public function test_it_can_set_patterns()
    {
        PatternRegistry::pattern('year', '\d{4}');
        PatternRegistry::pattern('month', '\d{2}');

        PatternRegistry::patterns(
            [
                'id' => '\w+',
                'uuid' => '[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}',
                'year' => 'overwritten',
            ]
        );

        $this->assertEquals('overwritten', PatternRegistry::getPattern('year'));
        $this->assertEquals('\d{2}', PatternRegistry::getPattern('month'));
        $this->assertEquals('\w+', PatternRegistry::getPattern('id'));
        $this->assertEquals(
            '[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}',
            PatternRegistry::getPattern('uuid')
        );
    }
}
