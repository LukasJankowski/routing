<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Constraints;

use LukasJankowski\Routing\Utilities\Path;
use LukasJankowski\Routing\Matchers\RouteMatcherInterface;
use Symfony\Component\HttpFoundation\Response;

use const PHP_INT_MAX;

final class SegmentRouteConstraint extends AbstractRouteConstraint
{
    public function validate(mixed $constraints = null): bool
    {
        foreach ($this->route->parsedParameters as $name => $props) {
            $value = $props['value'];

            if ($value === null || is_array($value)) {
                $this->route->parsedParameters[$name] = $value;
                continue;
            }

            $value = str_contains($value, '/') ? Path::split($value) : $value;

            $this->route->parsedParameters[$name] = $props['wildcard'] && ! is_array($value) ? [$value] : $value;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return Response::$statusTexts[Response::HTTP_NOT_FOUND];
    }

    public function getErrorCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
