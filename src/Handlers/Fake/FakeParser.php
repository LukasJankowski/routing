<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers\Fake;

use LukasJankowski\Routing\Handlers\ParserInterface;

final class FakeParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(array $routes): array
    {
        return [];
    }
}
