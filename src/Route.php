<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Constraints\SegmentConstraint;
use Serializable;

final class Route implements Serializable
{
    public mixed $parsedPath = null;

    public array $parsedParameters = [];

    /**
     * Route constructor.
     *
     * @param array<string,mixed> $constraints
     * @param array $middlewares
     * @param array<string,mixed> $defaults
     */
    public function __construct(
        private string $path,
        private mixed $action = null,
        private ?string $name = null,
        private array $constraints = [],
        private array $middlewares = [],
        private array $defaults = [],
    ) {
    }

    /**
     * @inheritDoc
     */
    public function serialize(): string
    {
        return serialize(get_object_vars($this));
    }

    /**
     * @inheritDoc
     */
    public function unserialize($data): void
    {
        [
            'path' => $this->path,
            'action' => $this->action,
            'name' => $this->name,
            'constraints' => $this->constraints,
            'middlewares' => $this->middlewares,
            'defaults' => $this->defaults,
            'parsedPath' => $this->parsedPath,
            'parsedParameters' => $this->parsedParameters,
        ] = unserialize($data);
    }

    /**
     * Getter.
     */
    public function getMethods(): array
    {
        return $this->getConstraints(MethodConstraint::class);
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
    public function getHost(): ?string
    {
        return $this->getConstraints(HostConstraint::class);
    }

    /**
     * Getter.
     */
    public function getSchemes(): array
    {
        return $this->getConstraints(SchemeConstraint::class);
    }

    /**
     * Getter.
     */
    public function getConstraints(?string $key = null): mixed
    {
        return $key ? $this->constraints[$key] ?? null : $this->constraints;
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
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Getter.
     */
    public function getSegmentConstraints(): array
    {
        return $this->getConstraints(SegmentConstraint::class);
    }
}
