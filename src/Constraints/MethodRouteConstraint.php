<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use Symfony\Component\HttpFoundation\Response;

final class MethodRouteConstraint extends AbstractRouteConstraint
{
    public function validate(mixed $constraints = null): bool
    {
        return in_array($this->request->method, $this->route->getMethods(), true);
    }

    public function getErrorMessage(): string
    {
        return 'constraint.method.mismatch';
    }

    public function getErrorCode(): int
    {
        return Response::HTTP_METHOD_NOT_ALLOWED;
    }
}
