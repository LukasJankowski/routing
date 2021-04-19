<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
final class Route
{
    /**
     * Route constructor.
     */
    public function __construct(
        public array|string $method,
        public string $path,
        public ?string $name = null,
        public ?string $host = null,
        public array|string $schemes = [],
        public array|string $constraints = [],
        public array|string $middlewares = [],
        public array $defaults = [],
    ) {
    }

    /**
     * Make the base route instance.
     */
    public function make(array $action): \LukasJankowski\Routing\Route
    {
        return new \LukasJankowski\Routing\Route(
            $this->method,
            $this->path,
            $action,
            $this->name,
            $this->host,
            $this->schemes,
            $this->constraints,
            $this->middlewares,
            $this->defaults
        );
    }
}
