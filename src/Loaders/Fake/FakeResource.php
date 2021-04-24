<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Fake;

use LukasJankowski\Routing\Loaders\ResourceInterface;

class FakeResource implements ResourceInterface
{
    /**
     * @inheritDoc
     */
    public function get(): array
    {
        return [];
    }
}
