<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use Symfony\Component\HttpFoundation\Response;

final class HostRouteConstraint extends AbstractRouteConstraint
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $constraints = null): bool
    {
        return $this->request->host === $this->route->getHost();
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return 'constraint.host.mismatch';
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
