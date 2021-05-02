<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use InvalidArgumentException;
use LukasJankowski\Routing\Utilities\Path;

final class Link
{
    /**
     * Generate a link from the route.
     *
     * @param array<string,array|string> $parameters
     */
    public static function to(Route $route, array $parameters = []): string
    {
        $path = $route->getPath();
        $dynamic = Path::extractDynamicSegments($path);
        $segments = array_keys($dynamic);

        $link = str_replace(
            preg_filter('/^/', '/', $segments),
            array_map(
                fn (string $segment, array $props) => self::setSegment($segment, $props, $parameters),
                $segments,
                $dynamic
            ),
            $path
        );

        return Path::normalize($link);
    }

    /**
     * Set the dynamic segment.
     *
     * @param array<string,mixed> $props
     * @param array<string,mixed> $parameters
     */
    private static function setSegment(string $segment, array $props, array $parameters): string
    {
        $parameter = $parameters[$props['name']] ?? null;

        self::checkOptional($segment, $parameter, $props['name']);

        if (! Path::isWildcardSegment($segment) && is_array($parameter)) {
            throw new InvalidArgumentException(
                sprintf('"%s" must not be an array to build this link.', $props['name'])
            );
        }

        return '/' . (is_array($parameter)
                ? implode('/', $parameter)
                : $parameter ?? '');
    }

    /**
     * Check the right parameters are passed in regard to optional segments.
     */
    private static function checkOptional(string $segment, mixed $parameter, string $name): void
    {
        if (! Path::isOptionalSegment($segment) && $parameter === null) {
            throw new InvalidArgumentException(
                sprintf('"%s" is required to build this link.', $name)
            );
        }

        if (! Path::isOptionalSegment($segment) && ! is_array($parameter) && Path::isWildcardSegment($segment)) {
            throw new InvalidArgumentException(
                sprintf('"%s" must be an array to build this link.', $name)
            );
        }
    }
}
