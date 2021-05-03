<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers\Regex;

use const ARRAY_FILTER_USE_KEY;
use LukasJankowski\Routing\Constraints\SegmentConstraint;
use LukasJankowski\Routing\Exceptions\BadRouteException;
use LukasJankowski\Routing\Handlers\AbstractMatcher;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\Router;

use LukasJankowski\Routing\Utilities\Path;
use const PREG_UNMATCHED_AS_NULL;

final class RegexMatcher extends AbstractMatcher
{
    /**
     * RegexMatcher constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->constraints[SegmentConstraint::class] = new SegmentConstraint();
    }

    /**
     * @inheritDoc
     *
     * @throws BadRouteException
     */
    public function matches(Route $route, Request $request): bool
    {
        if (! $this->matchPath($route, $request)) {
            return false;
        }

        $this->matchConstraints($route, $request);

        return true;
    }

    /**
     * Match the path.
     */
    private function matchPath(Route $route, Request $request): bool
    {
        $matches = [];
        $result = preg_match($route->getPrepared(), $request->path, $matches, PREG_UNMATCHED_AS_NULL);

        if (! $result || empty($matches)) {
            return false;
        }

        $route->setParameters(
            array_filter($matches, fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY)
        );

        foreach ($route->getParameters() as $name => $parameter) {
            $route->setParameters($name, $this->getParameter($route, $name, $parameter));
        }

        return true;
    }

    /**
     * Extract dynamic segment information.
     */
    private function getParameter(Route $route, string $name, ?string $parameter): array
    {
        $segment = [];
        preg_match(Router::specificDynamicSegmentPattern($name), $route->getPath(), $segment);
        $segment = $segment[0];

        return [
            'segment' => $segment,
            'optional' => Path::isOptionalSegment($segment),
            'wildcard' => Path::isWildcardSegment($segment),
            'value' => $parameter ?? $route->getDefaults()[$name] ?? null,
        ];
    }
}
