<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use LukasJankowski\Routing\Utilities\Path;
use Symfony\Component\HttpFoundation\Response;

final class SegmentConstraint extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $constraints = null): bool
    {
        foreach ($this->route->getParameters() as $name => $props) {
            $value = $props['value'];

            if ($value === null || is_array($value)) {
                $this->route->setParameters($name, $value);

                continue;
            }

            $value = str_contains($value, '/') ? Path::split($value) : $value;

            $this->route->setParameters($name, $props['wildcard'] && ! is_array($value) ? [$value] : $value);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessage(): string
    {
        return Response::$statusTexts[Response::HTTP_NOT_FOUND];
    }

    /**
     * @inheritDoc
     */
    public function getErrorCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
