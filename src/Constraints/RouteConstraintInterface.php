<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

interface RouteConstraintInterface
{
    /**
     * Setter.
     */
    public function setRoute(Route $route): void;

    /**
     * Setter.
     */
    public function setRequest(Request $request): void;

    /**
     * Validate the constraint.
     */
    public function validate(mixed $constraints = null): bool;

    /**
     * Getter.
     */
    public function getErrorMessage(): string;

    /**
     * Getter.
     */
    public function getErrorCode(): int;
}
