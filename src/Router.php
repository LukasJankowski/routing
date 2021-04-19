<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

class Router
{
    private const DYNAMIC_SEGMENTS_PATTERN = '#\1[\3\4]*?(\w+)(?:\:?([^\2\1]*+(?:(?R)[^\2\1]*)*+))\2#';

    private const SPECIFIC_SEGMENT_PATTERN = '#\1[\3\4]*?%s(?:\:([^\/]+))?\2#';

    public static function specificDynamicSegmentPattern(string $name): string
    {
        return self::dynamicSegmentPattern(sprintf(self::SPECIFIC_SEGMENT_PATTERN, $name));
    }

    public static function dynamicSegmentPattern(string $pattern = self::DYNAMIC_SEGMENTS_PATTERN): string
    {
        return str_replace(
            ['1', '2', '3', '4'],
            [
                self::openingIdentifier(),
                self::closingIdentifier(),
                self::wildcardIdentifier(),
                self::optionalIdentifier(),
            ],
            $pattern
        );
    }

    public static function dynamicFallbackPattern(): string
    {
        return '[^/]+';
    }

    public static function wildcardPattern(): string
    {
        return '.+';
    }

    public static function optionalPattern(): string
    {
        return '?';
    }

    public static function openingIdentifier(): string
    {
        return '{';
    }

    public static function closingIdentifier(): string
    {
        return '}';
    }

    public static function wildcardIdentifier(): string
    {
        return '*';
    }

    public static function optionalIdentifier(): string
    {
        return '?';
    }
}
