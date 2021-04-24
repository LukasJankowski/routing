<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Tests\fixtures;

use LukasJankowski\Routing\Attributes\Group;
use LukasJankowski\Routing\Attributes\Route;

#[Group('/prefix', 'prefix.', ['prefix'])]
final class AttributeClass
{
    #[Route(
        'get',
        '/',
        'name',
        'host.com',
        'https',
        ['to' => '\d+'],
        'test_middleware',
        ['to' => 'default']
    )]
    public function method(string $anything): void
    {
        //
    }

    #[Route(['post', 'put'], '/route')]
    public function test(): void
    {
        //
    }

    #[Route('get', '/test1')]
    #[Route('get', '/test2')]
    public function multiple(): void
    {
        //
    }

    #[Route('get', '/not-found')]
    private function hidden(): void
    {
        //
    }
}
