<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders;

interface ResourceInterface
{
    /**
     * Get the routes.
     */
    public function get(): array;
}
