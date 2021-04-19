<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Matchers;

use LukasJankowski\Routing\Constraints\HostRouteConstraint;
use LukasJankowski\Routing\Constraints\MethodRouteConstraint;
use LukasJankowski\Routing\Constraints\RouteConstraintInterface;
use LukasJankowski\Routing\Constraints\SchemeRouteConstraint;
use LukasJankowski\Routing\Constraints\SegmentRouteConstraint;
use LukasJankowski\Routing\Exceptions\BadRouteException;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\Router;
use LukasJankowski\Routing\Utilities\Path;
use RuntimeException;

use const ARRAY_FILTER_USE_KEY;
use const PREG_UNMATCHED_AS_NULL;

final class RegexRouteMatcher implements RouteMatcherInterface
{
    private array $constraints;

    public function __construct()
    {
        $this->constraints = [
            SegmentRouteConstraint::class => new SegmentRouteConstraint(),
            MethodRouteConstraint::class => new MethodRouteConstraint(),
            HostRouteConstraint::class => new HostRouteConstraint(),
            SchemeRouteConstraint::class => new SchemeRouteConstraint(),
        ];
    }

    public function matches(Route $route, Request $request): bool
    {
        if (! $this->matchPath($route, $request)) {
            return false;
        }

        $this->matchConstraints($route, $request);

        return true;
    }

    private function getValidator(string $constraint, Route $route, Request $request): RouteConstraintInterface
    {
        $validator = $this->getValidatorFromConstraint($constraint);
        $validator->setRoute($route);
        $validator->setRequest($request);

        return $validator;
    }

    private function getValidatorFromConstraint(string $constraint): RouteConstraintInterface
    {
        if (! is_subclass_of($constraint, RouteConstraintInterface::class)) {
            throw new RuntimeException(
                sprintf('Constraint "%s" must implement "%s".',
                        $constraint,
                        RouteConstraintInterface::class
                )
            );
        }

        return $this->constraints[$constraint] ?? new $constraint();
    }

    private function matchPath(Route $route, Request $request): bool
    {
        $matches = [];
        $result = preg_match($route->parsedPath, $request->path, $matches, PREG_UNMATCHED_AS_NULL);

        if (! $result || empty($matches)) {
            return false;
        }

        $route->parsedParameters = array_filter($matches, fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);

        foreach ($route->parsedParameters as $name => $parameter) {
            $route->parsedParameters[$name] = $this->getParameter($route, $name, $parameter);
        }

        return true;
    }


    private function getParameter(Route $route, string $name, ?string $parameter): array
    {
        $segment = [];
        preg_match(Router::specificDynamicSegmentPattern($name), $route->getPath(), $segment);
        $segment = $segment[0];

        return [
            'segment' => $segment,
            'optional' => Path::isOptionalSegment($segment),
            'wildcard' => Path::isWildcardSegment($segment),
            'value' => $parameter ?? $route->getDefaults()[$name] ?? null
        ];
    }

    private function matchConstraints(Route $route, Request $request): void
    {
        foreach ($route->getConstraints() as $constraint => $props) {
            $validator = $this->getValidator($constraint, $route, $request);

            if (! $validator->validate($props)) {
                throw new BadRouteException($validator->getErrorMessage(), $validator->getErrorCode());
            }
        }
    }

}
