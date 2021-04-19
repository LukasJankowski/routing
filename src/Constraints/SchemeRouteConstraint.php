<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use Symfony\Component\HttpFoundation\Response;

final class SchemeRouteConstraint extends AbstractRouteConstraint
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $constraints = null): bool
    {
        return $this->request->scheme === ''
            || in_array($this->request->scheme, $this->route->getSchemes(), true);
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return 'constraint.scheme.mismatch';
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
