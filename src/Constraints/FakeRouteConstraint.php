<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

final class FakeRouteConstraint extends AbstractRouteConstraint
{
    public function validate(mixed $constraints = null, bool $return = true): bool
    {
        return $return;
    }

    public function getErrorMessage(): string
    {
        return 'constraint.fake.message';
    }

    public function getErrorCode(): int
    {
        return 0;
    }
}
