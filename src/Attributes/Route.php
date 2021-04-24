<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Attributes;

use Attribute;
use LukasJankowski\Routing\RouteBuilder;

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
        $builder = RouteBuilder::match((array) $this->method, $this->path, $action);

        if ($this->name) {
            $builder->name($this->name);
        }

        if ($this->host) {
            $builder->host($this->host);
        }

        if (! empty($this->schemes)) {
            $builder->scheme($this->schemes);
        }

        return $builder->constraint($this->constraints)
            ->middleware($this->middlewares)
            ->default($this->defaults)
            ->build();
    }
}
