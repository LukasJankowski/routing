<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Utilities;

use LukasJankowski\Routing\Router;

use const PHP_INT_MAX;

final class Path
{
    /**
     * Normalize the path.
     */
    public static function normalize(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        return '/' . trim($path, '/#?');
    }

    /**
     * Split the path by '/'.
     */
    public static function split(string $path, int $count = PHP_INT_MAX): array
    {
        $segments = explode('/', str_starts_with($path, '/') ? substr($path, 1) : $path, $count);

        return $segments === [''] ? [] : $segments;
    }

    /**
     * Extract dynamic segments from the path.
     */
    public static function extractDynamicSegments(string $path): array
    {
        $matches = [];
        preg_match_all(Router::dynamicSegmentPattern(), $path, $matches);

        if (empty($matches)) {
            return [];
        }

        $properties = [];
        foreach ($matches[0] as $index => $match) {
            $name = $matches[1][$index];
            $pattern = $matches[2][$index];
            $properties[$match] = ['name' => $name, 'pattern' => $pattern === '' ? null : $pattern];
        }

        return $properties;
    }

    /**
     * Check if optional segment.
     */
    public static function isOptionalSegment(string $segment): bool
    {
        // {?var}, {*?var}
        return str_contains(substr($segment, 0, 3), Router::optionalIdentifier());
    }

    /**
     * Check if wildcard segment.
     */
    public static function isWildcardSegment(string $segment): bool
    {
        // {*var}, {?*var}
        return str_contains(substr($segment, 0, 3), Router::wildcardIdentifier());
    }
}
