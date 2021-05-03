<?php

namespace Handlers\Regex;

use LukasJankowski\Routing\Handlers\Regex\RegexParser;
use LukasJankowski\Routing\PatternRegistry;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class RegexParserTest extends TestCase
{
    private RegexParser $parser;

    protected function setUp(): void
    {
        $this->parser = new RegexParser();
    }

    public function provideRoutes(): array
    {
        return [
            [
                'given' => '/',
                'expected' => '#^/?$#'
            ],
            [
                'given' => '/path',
                'expected' => '#^/?path$#'
            ],
            [
                'given' => '/{var}',
                'expected' => '#^/?(?:/(?<var>[^/]+))$#'
            ],
            [
                'given' => '/nested/{var}',
                'expected' => '#^/?nested(?:/(?<var>[^/]+))$#'
            ],
            [
                'given' => '/{var}/nested',
                'expected' => '#^/?(?:/(?<var>[^/]+))/nested$#'
            ],
            [
                'given' => '/{double}/{var}',
                'expected' => '#^/?(?:/(?<double>[^/]+))(?:/(?<var>[^/]+))$#'
            ],
            [
                'given' => '/in/{between}/nested',
                'expected' => '#^/?in(?:/(?<between>[^/]+))/nested$#'
            ],
            [
                'given' => '/{var:\d+}',
                'expected' => '#^/?(?:/(?<var>\d+))$#'
            ],
            [
                'given' => '/{var:\d{4}}',
                'expected' => '#^/?(?:/(?<var>\d{4}))$#'
            ],
            [
                'given' => '/{var:[a-z]+}',
                'expected' => '#^/?(?:/(?<var>[a-z]+))$#'
            ],
            [
                'given' => '/nested/{var:\d+}',
                'expected' => '#^/?nested(?:/(?<var>\d+))$#'
            ],
            [
                'given' => '/{?var}',
                'expected' => '#^/?(?:/(?<var>[^/]+))?$#'
            ],
            [
                'given' => '/{?var:\d+}',
                'expected' => '#^/?(?:/(?<var>\d+))?$#'
            ],
            [
                'given' => '/static/{?var}',
                'expected' => '#^/?static(?:/(?<var>[^/]+))?$#'
            ],
            [
                'given' => '/static/{?var:\d+}',
                'expected' => '#^/?static(?:/(?<var>\d+))?$#'
            ],
            [
                'given' => '/{?var}/static',
                'expected' => '#^/?(?:/(?<var>[^/]+))?/static$#'
            ],
            [
                'given' => '/{?var}/{?test}',
                'expected' => '#^/?(?:/(?<var>[^/]+))?(?:/(?<test>[^/]+))?$#'
            ],
            [
                'given' => '/in/{?var}/between',
                'expected' => '#^/?in(?:/(?<var>[^/]+))?/between$#'
            ],
            [
                'given' => '/{*var}',
                'expected' => '#^/?(?:/(?<var>.+))$#'
            ],
            [
                'given' => '/{*ignored:\d+}',
                'expected' => '#^/?(?:/(?<ignored>.+))$#'
            ],
            [
                'given' => '/static/{*var}',
                'expected' => '#^/?static(?:/(?<var>.+))$#'
            ],
            [
                'given' => '/{*early}/static',
                'expected' => '#^/?(?:/(?<early>.+))/static$#'
            ],
            [
                'given' => '/in/{*var}/between',
                'expected' => '#^/?in(?:/(?<var>.+))/between$#'
            ],
            [
                'given' => '/{*two}/{*wildcards}',
                'expected' => '#^/?(?:/(?<two>.+))(?:/(?<wildcards>.+))$#'
            ],
            [
                'given' => '/{*?var}',
                'expected' => '#^/?(?:/(?<var>.+))?$#'
            ],
            [
                'given' => '/{?*var}',
                'expected' => '#^/?(?:/(?<var>.+))?$#'
            ],
            [
                'given' => '/static/{?*var}',
                'expected' => '#^/?static(?:/(?<var>.+))?$#'
            ],
            [
                'given' => '/{?*var}/static',
                'expected' => '#^/?(?:/(?<var>.+))?/static$#'
            ],
            [
                'given' => '/combo/{var}/{?opt}/{*wildcard}',
                'expected' => '#^/?combo(?:/(?<var>[^/]+))(?:/(?<opt>[^/]+))?(?:/(?<wildcard>.+))$#'
            ],
            [
                'given' => '/combo/{?opt}/{*wildcard}/{var}',
                'expected' => '#^/?combo(?:/(?<opt>[^/]+))?(?:/(?<wildcard>.+))(?:/(?<var>[^/]+))$#'
            ],
            [
                'given' => '/combo/{?opt}/{?*wildcard}/{?var}',
                'expected' => '#^/?combo(?:/(?<opt>[^/]+))?(?:/(?<wildcard>.+))?(?:/(?<var>[^/]+))?$#'
            ],
            [
                'given' => '/combo/{?*wc}/static/{var}/{?opt}',
                'expected' => '#^/?combo(?:/(?<wc>.+))?/static(?:/(?<var>[^/]+))(?:/(?<opt>[^/]+))?$#'
            ],
        ];
    }

    /**
     * @dataProvider provideRoutes
     */
    public function test_it_can_parse_routes($given, $expected)
    {
        /** @var Route $parsed */
        $parsed = $this->parser->parse([RouteBuilder::get($given)->build()])[0];

        $this->assertEquals($expected, $parsed->getPrepared());
    }

    public function test_it_can_parse_routes_with_pattern_registry()
    {
        PatternRegistry::pattern('year', '\d{4}');
        PatternRegistry::pattern('month', '\d{2}');
        PatternRegistry::pattern('day', '\d{2}');

        /** @var Route $parsed */
        $parsed = $this->parser->parse([RouteBuilder::get('/{year:year}/{month:\d{2}}/{day:day}')->build()])[0];

        $this->assertEquals(
            '#^/?(?:/(?<year>\d{4}))(?:/(?<month>\d{2}))(?:/(?<day>\d{2}))$#',
            $parsed->getPrepared()
        );
    }

    public function test_it_doesnt_parse_when_already_parsed()
    {
        $route = RouteBuilder::get('/')->build();
        $route->setPrepared('/some-parsed-path');

        /** @var Route $parsed */
        $parsed = $this->parser->parse([$route])[0];


        $this->assertEquals('/some-parsed-path', $parsed->getPrepared());
        $this->assertEquals($route, $parsed);
    }
}
