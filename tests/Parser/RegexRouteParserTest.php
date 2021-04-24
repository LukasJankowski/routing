<?php

namespace Parser;

use LukasJankowski\Routing\Parser\RegexRouteParser;
use LukasJankowski\Routing\Parser\RouteParserInterface;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use PHPUnit\Framework\TestCase;

class RegexRouteParserTest extends TestCase
{
    public function test_it_can_be_instantiated()
    {
        $parser = new RegexRouteParser();

        $this->assertInstanceOf(RegexRouteParser::class, $parser);
        $this->assertInstanceOf(RouteParserInterface::class, $parser);
    }

    public function test_it_can_parse_routes()
    {
        $parser = new RegexRouteParser();

        $paths = [
            '/' => '#^/?$#',
            '/path' => '#^/?path$#',
            '/{var}' => '#^/?(?:/(?<var>[^/]+))$#',
            '/nested/{var}' => '#^/?nested(?:/(?<var>[^/]+))$#',
            '/{var}/nested' => '#^/?(?:/(?<var>[^/]+))/nested$#',
            '/{double}/{var}' => '#^/?(?:/(?<double>[^/]+))(?:/(?<var>[^/]+))$#',
            '/in/{between}/nested' => '#^/?in(?:/(?<between>[^/]+))/nested$#',

            '/{var:\d+}' => '#^/?(?:/(?<var>\d+))$#',
            '/{var:\d{4}}' => '#^/?(?:/(?<var>\d{4}))$#',
            '/{var:[a-z]+}' => '#^/?(?:/(?<var>[a-z]+))$#',
            '/nested/{var:\d+}' => '#^/?nested(?:/(?<var>\d+))$#',

            '/{?var}' => '#^/?(?:/(?<var>[^/]+))?$#',
            '/{?var:\d+}' => '#^/?(?:/(?<var>\d+))?$#',
            '/static/{?var}' => '#^/?static(?:/(?<var>[^/]+))?$#',
            '/static/{?var:\d+}' => '#^/?static(?:/(?<var>\d+))?$#',
            '/{?var}/static' => '#^/?(?:/(?<var>[^/]+))?/static$#',
            '/{?var}/{?test}' => '#^/?(?:/(?<var>[^/]+))?(?:/(?<test>[^/]+))?$#',
            '/in/{?var}/between' => '#^/?in(?:/(?<var>[^/]+))?/between$#',

            '/{*var}' => '#^/?(?:/(?<var>.+))$#',
            '/{*ignored:\d+}' => '#^/?(?:/(?<ignored>.+))$#',
            '/static/{*var}' => '#^/?static(?:/(?<var>.+))$#',
            '/{*early}/static' => '#^/?(?:/(?<early>.+))/static$#',
            '/in/{*var}/between' => '#^/?in(?:/(?<var>.+))/between$#',
            '/{*two}/{*wildcards}' => '#^/?(?:/(?<two>.+))(?:/(?<wildcards>.+))$#',

            '/{*?var}' => '#^/?(?:/(?<var>.+))?$#',
            '/{?*var}' => '#^/?(?:/(?<var>.+))?$#',
            '/static/{?*var}' => '#^/?static(?:/(?<var>.+))?$#',
            '/{?*var}/static' => '#^/?(?:/(?<var>.+))?/static$#',

            '/combo/{var}/{?opt}/{*wildcard}' => '#^/?combo(?:/(?<var>[^/]+))(?:/(?<opt>[^/]+))?(?:/(?<wildcard>.+))$#',
            '/combo/{?opt}/{*wildcard}/{var}' => '#^/?combo(?:/(?<opt>[^/]+))?(?:/(?<wildcard>.+))(?:/(?<var>[^/]+))$#',
            '/combo/{?opt}/{?*wildcard}/{?var}' => '#^/?combo(?:/(?<opt>[^/]+))?(?:/(?<wildcard>.+))?(?:/(?<var>[^/]+))?$#',
            '/combo/{?*wc}/static/{var}/{?opt}' => '#^/?combo(?:/(?<wc>.+))?/static(?:/(?<var>[^/]+))(?:/(?<opt>[^/]+))?$#',
        ];

        foreach ($paths as $path => $expected) {
            $route = RouteBuilder::get($path)->build();
            /** @var Route $parsed */
            $parsed = $parser->parse([$route])[0];

            $this->assertEquals($route->getPath(), $parsed->getPath());
            $this->assertEquals($expected, $parsed->parsedPath);
        }
    }

    public function test_it_doesnt_parse_when_already_parsed()
    {
        $parser = new RegexRouteParser();

        $route = RouteBuilder::get('/')->build();
        $route->parsedPath = '/some-parsed-path';

        /** @var Route $parsed */
        $parsed = $parser->parse([$route])[0];


        $this->assertEquals('/some-parsed-path', $parsed->parsedPath);
        $this->assertEquals($route, $parsed);
    }
}
