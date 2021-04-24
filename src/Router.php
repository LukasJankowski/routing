<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

class Router
{
    private const DYNAMIC_SEGMENTS_PATTERN = '#\1[\3\4]*?(\w+)(?:\:?([^\2\1]*+(?:(?R)[^\2\1]*)*+))\2#';

    private const SPECIFIC_SEGMENT_PATTERN = '#\1[\3\4]*?%s(?:\:([^\/]+))?\2#';

    /** @var array<Collection> */
    private array $collections = [];

    /**
     * Resolve the request against all collections.
     */
    public function resolve(Request $request): false|RouteMatch
    {
        foreach ($this->collections as $collection) {
            if ($route = $collection->match($request)) {
                return $this->makeMatch($route, $request);
            }
        }

        return false;
    }

    /**
     * Add many collections.
     *
     * @param array<Collection> $collections
     */
    public function addMany(array $collections): void
    {
        array_map([$this, 'add'], $collections);
    }

    /**
     * Add a collection.
     */
    public function add(Collection $collection): void
    {
        $this->collections[] = $collection;
    }

    /**
     * Get a specific dynamic segment from the path (pattern).
     */
    public static function specificDynamicSegmentPattern(string $name): string
    {
        return self::dynamicSegmentPattern(sprintf(self::SPECIFIC_SEGMENT_PATTERN, $name));
    }

    /**
     * Get all dynamic segments from the path (pattern).
     */
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

    /**
     * The fallback regex for dynamic segments.
     */
    public static function dynamicFallbackPattern(): string
    {
        return '[^/]+';
    }

    /**
     * Getter.
     */
    public static function wildcardPattern(): string
    {
        return '.+';
    }

    /**
     * Getter.
     */
    public static function optionalPattern(): string
    {
        return '?';
    }

    /**
     * Getter.
     */
    public static function openingIdentifier(): string
    {
        return '{';
    }

    /**
     * Getter.
     */
    public static function closingIdentifier(): string
    {
        return '}';
    }

    /**
     * Getter.
     */
    public static function wildcardIdentifier(): string
    {
        return '*';
    }

    /**
     * Getter.
     */
    public static function optionalIdentifier(): string
    {
        return '?';
    }

    /**
     * Make a match from the route & request.
     */
    public function makeMatch(Route $route, Request $request): RouteMatch
    {
        return new RouteMatch(
            $request->path,
            $route->getPath(),
            $route->getAction(),
            $route->getName(),
            $route->getMiddlewares(),
            $route->parsedParameters
        );
    }

    /**
     * Getter.
     */
    public function getCollections(): array
    {
        return $this->collections;
    }
}
