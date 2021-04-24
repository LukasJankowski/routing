<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use InvalidArgumentException;
use LukasJankowski\Routing\Handlers\DefaultRouteHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeRouteParser;
use LukasJankowski\Routing\Handlers\Regex\RegexRouteMatcher;
use LukasJankowski\Routing\Handlers\Regex\RegexRouteParser;
use LukasJankowski\Routing\Handlers\RouteHandlerInterface;
use LukasJankowski\Routing\Loaders\DefaultRouteLoader;
use LukasJankowski\Routing\Loaders\RouteCacheInterface;
use LukasJankowski\Routing\Loaders\RouteLoaderInterface;
use LukasJankowski\Routing\Loaders\RouteResourceInterface;

final class RouteCollectionBuilder
{
    public const HANDLERS = [
        'regex' => [RegexRouteMatcher::class, RegexRouteParser::class],
        'fake' => [FakeRouteMatcher::class, FakeRouteParser::class]
    ];

    private RouteHandlerInterface $handler;

    private ?RouteLoaderInterface $loader = null;

    private ?RouteCacheInterface $cache = null;

    private ?RouteResourceInterface $resource = null;

    private string $name = 'default';

    /**
     * RouteCollectionBuilder constructor.
     */
    private function __construct(string|RouteHandlerInterface $handler)
    {
        $this->handler = is_string($handler)
            ? $this->handlerFromString($handler)
            : $handler;
    }

    /**
     * Setter.
     */
    public static function handler(string|RouteHandlerInterface $handler): self
    {
        return new self($handler);
    }

    /**
     * Setter.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Setter.
     */
    public function loader(RouteLoaderInterface $loader): self
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Setter.
     */
    public function cache(RouteCacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Setter.
     */
    public function resource(RouteResourceInterface $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Build the collection.
     */
    public function build(): RouteCollection
    {
        return new RouteCollection(
            $this->handler,
            $this->loader ?? $this->makeLoader(),
            $this->name
        );
    }

    /**
     * Resolve the handler from string.
     */
    private function handlerFromString(string $handler): RouteHandlerInterface
    {
        if (! array_key_exists($handler, self::HANDLERS)) {
            throw new InvalidArgumentException(
                sprintf('Handler "%s" not available.', $handler)
            );
        }

        return new DefaultRouteHandler(
            ...array_map(fn ($class) => new $class(), self::HANDLERS[$handler])
        );
    }

    /**
     * Make a loader if possible.
     */
    private function makeLoader(): ?RouteLoaderInterface
    {
        return $this->cache === null && $this->resource === null
            ? null
            : new DefaultRouteLoader($this->cache, $this->resource);
    }
}
