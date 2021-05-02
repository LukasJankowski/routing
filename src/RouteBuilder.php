<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use LukasJankowski\Routing\Constraints\HostConstraint;
use LukasJankowski\Routing\Constraints\MethodConstraint;
use LukasJankowski\Routing\Constraints\SchemeConstraint;
use LukasJankowski\Routing\Constraints\SegmentConstraint;
use LukasJankowski\Routing\Utilities\Method;
use LukasJankowski\Routing\Utilities\Path;
use LukasJankowski\Routing\Utilities\Scheme;

final class RouteBuilder
{
    /** @var array<array<string,mixed>> */
    private static array $stack = [];

    private string $path;

    private ?string $name = null;

    private array $middlewares = [];

    /** @var array<string,mixed> */
    private array $defaults = [];

    /** @var array<string,mixed> */
    private array $constraints = [];

    /**
     * RouteBuilder constructor.
     *
     * @param array<string> $methods
     */
    private function __construct(array $methods, string $path, private mixed $action = null)
    {
        $this->constraint(MethodConstraint::class, Method::normalize($methods));
        $this->constraint(SegmentConstraint::class, []);

        $this->path = Path::normalize($path);
    }

    /**
     * Get route.
     */
    public static function get(string $path, mixed $action = null): self
    {
        return self::match([Request::METHOD_GET], $path, $action);
    }

    /**
     * Post route.
     */
    public static function post(string $path, mixed $action = null): self
    {
        return self::match([Request::METHOD_POST], $path, $action);
    }

    /**
     * Put route.
     */
    public static function put(string $path, mixed $action = null): self
    {
        return self::match([Request::METHOD_PUT], $path, $action);
    }

    /**
     * Patch route.
     */
    public static function patch(string $path, mixed $action = null): self
    {
        return self::match([Request::METHOD_PATCH], $path, $action);
    }

    /**
     * Delete route.
     */
    public static function delete(string $path, mixed $action = null): self
    {
        return self::match([Request::METHOD_DELETE], $path, $action);
    }

    /**
     * Head route.
     */
    public static function head(string $path, mixed $action = null): self
    {
        return self::match([Request::METHOD_HEAD], $path, $action);
    }

    /**
     * Options route.
     */
    public static function options(string $path, mixed $action = null): self
    {
        return self::match([Request::METHOD_OPTIONS], $path, $action);
    }

    /**
     * Build a route with multiple methods.
     *
     * @param array<string> $methods
     */
    public static function match(array $methods, string $path, mixed $action = null): self
    {
        return new self($methods, $path, $action);
    }

    public static function group(array $properties, callable $closure): void
    {
        self::$stack[] = $properties;

        $closure();

        array_pop(self::$stack);
    }

    /**
     * Build the route.
     */
    public function build(): Route
    {
        $this->applyStacks();

        return new Route(
            $this->path,
            $this->action,
            $this->name,
            $this->constraints,
            $this->middlewares,
            $this->defaults
        );
    }

    /**
     * Set defaults for segments.
     *
     * @param array<string,mixed> $defaults
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
     *
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
     *
     * @param array<string>|string $schemes
     */
    public function scheme(array|string $schemes): self
    {
        $this->constraint(SchemeConstraint::class, (array) Scheme::normalize($schemes));

        return $this;
    }

    /**
     * Add segment / custom constraints to the route.
     *
     * @param array<string,mixed>|string $constraints
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
    public function action(mixed $action): self
    {
        $this->action = $action;

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
        $this->constraint(HostConstraint::class, $host);

        return $this;
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

        $this->constraints[SegmentConstraint::class] = array_merge(
            $this->constraints[SegmentConstraint::class],
            [$config]
        );
    }

    /**
     * Apply group stacks to current route.
     */
    private function applyStacks(): void
    {
        foreach (array_reverse(self::$stack) as $props) {
            $path = $props['path'] ?? null;
            $name = $props['name'] ?? null;
            $middleware = $props['middleware'] ?? null;

            if ($path) {
                $this->path = Path::normalize(Path::normalize($path) . $this->path);
            }

            if ($name) {
                $this->name($name . $this->name);
            }

            if ($middleware) {
                $this->middleware($middleware);
            }
        }
    }
}
