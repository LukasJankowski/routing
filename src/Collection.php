<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use LukasJankowski\Routing\Handlers\HandlerInterface;
use LukasJankowski\Routing\Loaders\LoaderInterface;

class Collection implements IteratorAggregate, Countable
{
    /** @var array<Route> */
    protected array $routes = [];

    private bool $parsed = false;

    /**
     * Collection constructor.
     */
    public function __construct(
        private HandlerInterface $handler,
        private ?LoaderInterface $loader = null,
        private string $name = 'default'
    ) {
        if ($this->loader) {
            $this->fromLoader($loader);
        }
    }

    /**
     * Fetch routes from resource.
     */
    public function fromLoader(LoaderInterface $loader): void
    {
        $this->addMany($loader->get());
    }

    /**
     * Add many routes.
     *
     * @param array<Route> $routes
     */
    public function addMany(array $routes): void
    {
        array_map([$this, 'add'], $routes);
    }

    /**
     * Match the request against the routes
     */
    public function match(Request $request): false|Route
    {
        if (! $this->parsed) {
            $this->parse();
        }

        foreach ($this->routes as $route) {
            if ($this->handler->matches($route, $request)) {
                return $route;
            }
        }

        return false;
    }

    /**
     * Parse the routes.
     */
    public function parse(): void
    {
        $this->routes = $this->handler->parse($this->routes);

        $this->parsed = true;
    }

    /**
     * Add a route.
     */
    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Cache the routes.
     */
    public function cache(): void
    {
        if ($this->loader !== null) {
            $this->loader->set($this->routes);
        }
    }

    /**
     * Getter.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Getter.
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Getter.
     */
    public function isParsed(): bool
    {
        return $this->parsed;
    }

    /**
     * Filter the routes by name.
     */
    public function getRouteByName(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->routes);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->routes);
    }
}
