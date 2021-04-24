<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers\Regex;

use const ARRAY_FILTER_USE_KEY;
use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Constraints\ConstraintInterface;
use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Constraints\SegmentConstraint;
use LukasJankowski\Routing\Exceptions\BadRouteException;
use LukasJankowski\Routing\Handlers\MatcherInterface;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\Router;
use LukasJankowski\Routing\Utilities\Path;

use const PREG_UNMATCHED_AS_NULL;
use RuntimeException;

final class RegexMatcher implements MatcherInterface
{
    /** @var array<string,ConstraintInterface> */
    private array $constraints;

    /**
     * RegexMatcher constructor.
     */
    public function __construct()
    {
        $this->constraints = [
            SegmentConstraint::class => new SegmentConstraint(),
            MethodConstraint::class => new MethodConstraint(),
            HostConstraint::class => new HostConstraint(),
            SchemeConstraint::class => new SchemeConstraint(),
        ];
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
     * Prepare the constraint.
     */
    private function getValidator(string $constraint, Route $route, Request $request): ConstraintInterface
    {
        $validator = $this->getValidatorFromConstraint($constraint);
        $validator->setRoute($route);
        $validator->setRequest($request);

        return $validator;
    }

    /**
     * Get the constraint.
     */
    private function getValidatorFromConstraint(string $constraint): ConstraintInterface
    {
        if (! is_subclass_of($constraint, ConstraintInterface::class)) {
            throw new RuntimeException(
                sprintf(
                    'Constraint "%s" must implement "%s".',
                    $constraint,
                    ConstraintInterface::class
                )
            );
        }

        return $this->constraints[$constraint] ?? new $constraint();
    }

    /**
     * Match the path.
     */
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
            'value' => $parameter ?? $route->getDefaults()[$name] ?? null
        ];
    }

    /**
     * Match constraints.
     *
     * @throws BadRouteException
     */
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
