<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

final class PatternRegistry
{
    /** @var array<string,string> */
    private static array $patterns = [];

    /**
     * Get a registered pattern from the registry.
     */
    public static function getPattern(string $name): ?string
    {
        return self::$patterns[$name] ?? null;
    }

    /**
     * Set a pattern in the registry.
     */
    public static function pattern(string $name, string $pattern): void
    {
        self::$patterns[$name] = $pattern;
    }

    /**
     * Set many patterns in the registry.
     *
     * @param array<string,string> $patterns
     */
    public static function patterns(array $patterns): void
    {
        foreach ($patterns as $name => $pattern) {
            self::pattern($name, $pattern);
        }
    }
}
