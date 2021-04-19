<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Parser;

interface RouteParserInterface
{
    public function parse(array $routes): array;
}
