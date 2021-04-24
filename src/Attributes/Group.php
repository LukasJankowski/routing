<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Group
{
    /**
     * Group constructor.
     */
    public function __construct(
        public string $path,
        public ?string $name = null,
        public array|string $middlewares = [],
    ) {
    }

    /**
     * Get the defined properties.
     */
    public function properties(): array
    {
        return [
            'path' => $this->path,
            'name' => $this->name,
            'middleware' => $this->middlewares
        ];
    }
}
