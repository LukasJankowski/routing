<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers;

use LukasJankowski\Routing\Constraints\ConstraintInterface;
use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Exceptions\BadRouteException;
use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;
use RuntimeException;

abstract class AbstractMatcher implements MatcherInterface
{
    /** @var array<string,ConstraintInterface> */
    protected array $constraints;

    /**
     * RegexMatcher constructor.
     */
    public function __construct()
    {
        $this->constraints = [
            MethodConstraint::class => new MethodConstraint(),
            HostConstraint::class => new HostConstraint(),
            SchemeConstraint::class => new SchemeConstraint(),
        ];
    }

    /**
     * Prepare the constraint.
     */
    protected function getValidator(string $constraint, Route $route, Request $request): ConstraintInterface
    {
        $validator = $this->getValidatorFromConstraint($constraint);
        $validator->setRoute($route);
        $validator->setRequest($request);

        return $validator;
    }

    /**
     * Get the constraint.
     */
    protected function getValidatorFromConstraint(string $constraint): ConstraintInterface
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
     * Match constraints.
     *
     * @throws BadRouteException
     */
    protected function matchConstraints(Route $route, Request $request): void
    {
        foreach ($route->getConstraints() as $constraint => $props) {
            $validator = $this->getValidator($constraint, $route, $request);

            if (! $validator->validate($props)) {
                throw new BadRouteException($validator->getErrorMessage(), $validator->getErrorCode());
            }
        }
    }
}
