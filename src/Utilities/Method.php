<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Utilities;

use InvalidArgumentException;
use LukasJankowski\Routing\Request;

final class Method
{
    public static function normalize(array|string $methods): array|string
    {
        if (is_string($methods) && strtoupper($methods) === 'ANY') {
            return Request::METHODS;
        }

        $methodList = array_map(
            function ($method) {
                $method = strtoupper($method);
                if (! in_array($method, Request::METHODS)) {
                    throw new InvalidArgumentException(
                        sprintf('Method "%s" is not in "%s"::METHODS.', $method, Request::class)
                    );
                }

                return $method;
            },
            is_string($methods) ? [$methods] : $methods
        );

        return is_string($methods) ? $methodList[0] : $methodList;
    }
}
