<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Tests\fixtures;

use LukasJankowski\Routing\Attributes\Route;

final class AttributeClass
{
    #[Route('get', '/')]
    public function method(string $anything): void
    {
        //
    }

    #[Route(['post', 'put'], '/route')]
    public function test(): void
    {

    }

    #[Route('get', '/test1')]
    #[Route('get', '/test2')]
    public function multiple(): void
    {

    }

    #[Route('get', '/not-found')]
    private function hidden(): void
    {

    }
}
