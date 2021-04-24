<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

final class RouteMatch
{
    /**
     * RouteMatch constructor.
     *
     * @param array $middlewares
     * @param array<string,mixed> $parameters
     */
    public function __construct(
        private string $path,
        private string $route,
        private mixed $action,
        private ?string $name,
        private array $middlewares,
        private array $parameters = [],
    ) {
    }

    /**
     * Getter.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Getter.
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Getter.
     */
    public function getAction(): mixed
    {
        return $this->action;
    }

    /**
     * Getter.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Getter.
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Getter.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
