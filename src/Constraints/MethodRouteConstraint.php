<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use Symfony\Component\HttpFoundation\Response;

final class MethodRouteConstraint extends AbstractRouteConstraint
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $constraints = null): bool
    {
        return in_array($this->request->method, $this->route->getMethods(), true);
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return 'constraint.method.mismatch';
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): int
    {
        return Response::HTTP_METHOD_NOT_ALLOWED;
    }
}
