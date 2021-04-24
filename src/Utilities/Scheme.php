<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Utilities;

use InvalidArgumentException;
use LukasJankowski\Routing\Request;

final class Scheme
{
    /**
     * Normalize the scheme.
     *
     * @param array<string>|string $schemes
     */
    public static function normalize(string|array $schemes): string|array
    {
        if ($schemes === []) {
            return Request::SCHEMES;
        }

        $schemeList = array_map(
            function ($scheme) {
                $scheme = strtoupper($scheme);
                if (! in_array($scheme, Request::SCHEMES)) {
                    throw new InvalidArgumentException(
                        sprintf('Scheme "%s" is not in "%s"::SCHEMES.', $scheme, Request::class)
                    );
                }

                return $scheme;
            },
            is_string($schemes) ? [$schemes] : $schemes
        );

        return is_string($schemes) ? $schemeList[0] : $schemeList;
    }
}
