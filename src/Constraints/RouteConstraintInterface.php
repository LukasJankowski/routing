<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

interface RouteConstraintInterface
{
    public function setRoute(Route $route): void;

    public function setRequest(Request $request): void;

    public function validate(mixed $constraints = null): bool;

    public function getErrorMessage(): string;

    public function getErrorCode(): int;
}
