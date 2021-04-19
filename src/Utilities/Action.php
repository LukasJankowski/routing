<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Utilities;

use InvalidArgumentException;

final class Action
{
    /**
     * Normalize the action.
     */
    public static function normalize(array $action): array
    {
        if (! is_callable($action, true) || ! method_exists($action[0], $action[1])) {
            throw new InvalidArgumentException('Action is not a valid callable.');
        }

        return $action;
    }
}
