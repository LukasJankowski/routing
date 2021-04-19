<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Parser;

use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\Router;
use LukasJankowski\Routing\Utilities\Path;

final class RegexRouteParser implements RouteParserInterface
{
    private const REGEX_OPENER = '#^/?';

    private const REGEX_CLOSER = '$#';

    public function parse(array $routes): array
    {
        /** @var Route $route */
        foreach ($routes as &$route) {
            // Routes are already parsed (e.g. loaded from cache)
            if ($route->parsedPath !== null) {
                return $routes;
            }

            $route = $this->parseRoute($route);
        }

        return $routes;
    }

    private function parseRoute(Route $route): Route
    {
        $path = $route->getPath();
        $constraints = $route->getSegmentConstraints();
        $dynamic = Path::extractDynamicSegments($path);
        $segments = array_keys($dynamic);

        $compiledRegex = str_replace(
            preg_filter('/^/', '/', $segments),
            array_map(
                fn (string $segment, array $props) => $this->ensurePattern($segment, $props, $constraints),
                $segments,
                $dynamic
            ),
            $path
        );

        $route->parsedPath = self::REGEX_OPENER . trim($compiledRegex, '/') . self::REGEX_CLOSER;

        return $route;
    }

    private function ensurePattern(string $segment, array $dynamic, array $constraints): string
    {
        $pattern = $dynamic['pattern'] ?: $this->patternFromConstraints($dynamic['name'], $constraints);
        $pattern ??= Router::dynamicFallbackPattern();

        return sprintf(
            '(?:/(?<%s>%s))%s',
            $dynamic['name'],
            Path::isWildcardSegment($segment) ? Router::wildcardPattern() : $pattern,
            Path::isOptionalSegment($segment) ? Router::optionalPattern() : ''
        );
    }

    private function patternFromConstraints(string $name, array $constraints): ?string
    {
        foreach ($constraints as $constraint) {
            if ($constraint['name'] === $name) {
                return $constraint['pattern'];
            }
        }

        return null;
    }
}
