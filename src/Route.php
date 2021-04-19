<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use LukasJankowski\Routing\Constraints\HostRouteConstraint;
use LukasJankowski\Routing\Constraints\MethodRouteConstraint;
use LukasJankowski\Routing\Constraints\SchemeRouteConstraint;
use LukasJankowski\Routing\Constraints\SegmentRouteConstraint;
use LukasJankowski\Routing\Utilities\Action;
use LukasJankowski\Routing\Utilities\Method;
use LukasJankowski\Routing\Utilities\Path;
use LukasJankowski\Routing\Utilities\Scheme;
use RuntimeException;
use Serializable;

final class Route implements Serializable
{
    public mixed $parsedPath = null;

    public array $parsedParameters = [];

    private string $path;

    private ?array $action = null;

    private ?string $name = null;

    private array $constraints = [];

    private array $middlewares = [];

    private array $defaults = [];

    /**
     * Route constructor.
     */
    public function __construct(
        array|string $methods,
        string $path,
        ?array $action = null,
        ?string $name = null,
        ?string $host = null,
        array|string $schemes = [],
        array|string $constraints = [],
        array|string $middlewares = [],
        array $defaults = [],
    ) {
        $this->constraint(MethodRouteConstraint::class, (array) Method::normalize($methods));

        $this->path = Path::normalize($path);

        if ($name) {
            $this->name($name);
        }

        if ($action) {
            $this->action($action);
        }

        if ($host) {
            $this->host($host);
        }

        if ($schemes) {
            $this->scheme($schemes);
        }

        $this->constraint(SegmentRouteConstraint::class, []);

        $this->middleware($middlewares)
            ->constraint($constraints)
            ->default($defaults);
    }

    /**
     * Set defaults for segments.
     */
    public function default(array $defaults): self
    {
        foreach ($defaults as $name => $default) {
            $this->defaults[$name] = $default;
        }

        return $this;
    }

    /**
     * Set middlewares for the route.
     */
    public function middleware(array|string $middlewares): self
    {
        $this->middlewares = array_merge(
            is_string($middlewares) ? [$middlewares] : $middlewares,
            $this->middlewares
        );

        return $this;
    }

    /**
     * Set the scheme for the route.
     */
    public function scheme(array|string $schemes): self
    {
        $this->constraint(SchemeRouteConstraint::class, (array) Scheme::normalize($schemes));

        return $this;
    }

    /**
     * Add segment / custom constraints to the route.
     */
    public function constraint(array|string $constraints, mixed $value = null): self
    {
        if (is_string($constraints)) {
            $this->addConstraint($constraints, $value);

            return $this;
        }

        foreach ($constraints as $constraint => $value) {
            $this->addConstraint($constraint, $value);
        }

        return $this;
    }

    /**
     * Set the action.
     */
    public function action(array $action): self
    {
        $this->action = Action::normalize($action);

        return $this;
    }

    /**
     * Set the name.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the host constraint.
     */
    public function host(string $host): self
    {
        $this->constraint(HostRouteConstraint::class, $host);

        return $this;
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
        return $this->getConstraints(MethodRouteConstraint::class);
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
    public function getAction(): array
    {
        if ($this->action === null) {
            throw new RuntimeException(
                sprintf('Action for "%s" must be set.', $this->name ?? $this->path)
            );
        }

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
        return $this->getConstraints(HostRouteConstraint::class);
    }

    /**
     * Getter.
     */
    public function getSchemes(): array
    {
        return $this->getConstraints(SchemeRouteConstraint::class);
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
        return $this->getConstraints(SegmentRouteConstraint::class);
    }

    /**
     * Add a constraint.
     */
    private function addConstraint(string $constraint, mixed $value): void
    {
        class_exists($constraint)
            ? $this->constraints[$constraint] = $value
            : $this->addSegmentConstraint($constraint, $value);
    }

    /**
     * Add a segment constraint.
     */
    private function addSegmentConstraint(string $constraint, false|string $pattern): void
    {
        $config = ['name' => $constraint, 'pattern' => $pattern];

        $this->constraints[SegmentRouteConstraint::class] = array_merge(
            $this->constraints[SegmentRouteConstraint::class],
            [$config]
        );
    }
}
