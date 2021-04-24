<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Fake;

use LukasJankowski\Routing\Loaders\CacheInterface;

final class FakeCache extends FakeResource implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function set(array $routes): void
    {
    }
}
