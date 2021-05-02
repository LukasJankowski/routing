<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers\Fixed;

use LukasJankowski\Routing\Handlers\ParserInterface;

final class FixedParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(array $routes): array
    {
        return $routes;
    }
}
