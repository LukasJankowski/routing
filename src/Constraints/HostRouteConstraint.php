<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use Symfony\Component\HttpFoundation\Response;

final class HostRouteConstraint extends AbstractRouteConstraint
{
    public function validate(mixed $constraints = null): bool
    {
        return $this->request->host === $this->route->getHost();
    }

    public function getErrorMessage(): string
    {
        return 'constraint.host.mismatch';
    }

    public function getErrorCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
