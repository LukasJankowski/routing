<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Tests\fixtures;

use LukasJankowski\Routing\Attributes\Route;

final class AlternateAttributeClass
{
    #[Route('get', '/')]
    public function method(string $anything): void
    {
        //
    }
}
