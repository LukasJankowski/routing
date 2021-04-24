<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

final class FakeConstraint extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $constraints = null, bool $return = true): bool
    {
        return $return;
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return 'constraint.fake.message';
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): int
    {
        return 0;
    }
}
