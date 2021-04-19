<?php

namespace Utilities;

use InvalidArgumentException;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Utilities\Scheme;
use PHPUnit\Framework\TestCase;

class SchemeTest extends TestCase
{
    public function test_it_normalizes_schemes()
    {
        $this->assertEquals('HTTPS', Scheme::normalize('hTTps'));
        $this->assertEquals(['HTTP', 'HTTPS'], Scheme::normalize(['http', 'HTTPS']));
        $this->assertEquals('', Scheme::normalize(''));
        $this->assertEquals(Request::SCHEMES, Scheme::normalize([]));

        $this->expectException(InvalidArgumentException::class);

        Scheme::normalize('unknown_scheme');
    }
}
