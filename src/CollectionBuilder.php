<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use InvalidArgumentException;
use LukasJankowski\Routing\Handlers\DefaultHandler;
use LukasJankowski\Routing\Handlers\Fake\FakeMatcher;
use LukasJankowski\Routing\Handlers\Fake\FakeParser;
use LukasJankowski\Routing\Handlers\Fixed\FixedMatcher;
use LukasJankowski\Routing\Handlers\Fixed\FixedParser;
use LukasJankowski\Routing\Handlers\HandlerInterface;
use LukasJankowski\Routing\Handlers\Regex\RegexMatcher;
use LukasJankowski\Routing\Handlers\Regex\RegexParser;
use LukasJankowski\Routing\Loaders\Array\ArrayCache;
use LukasJankowski\Routing\Loaders\Array\ArrayResource;
use LukasJankowski\Routing\Loaders\CacheInterface;
use LukasJankowski\Routing\Loaders\DefaultLoader;
use LukasJankowski\Routing\Loaders\Fake\FakeCache;
use LukasJankowski\Routing\Loaders\Fake\FakeResource;
use LukasJankowski\Routing\Loaders\LoaderInterface;
use LukasJankowski\Routing\Loaders\ResourceInterface;

final class CollectionBuilder
{
    public const HANDLERS = [
        'regex' => [RegexMatcher::class, RegexParser::class],
        'fake' => [FakeMatcher::class, FakeParser::class],
        'fixed' => [FixedMatcher::class, FixedParser::class],
    ];

    public const LOADERS = [
        'array' => [ArrayCache::class, ArrayResource::class],
        'fake' => [FakeCache::class, FakeResource::class]
    ];

    private HandlerInterface $handler;

    private ?LoaderInterface $loader = null;

    private ?CacheInterface $cache = null;

    private ?ResourceInterface $resource = null;

    private string $name = 'default';

    /**
     * CollectionBuilder constructor.
     */
    private function __construct(string|HandlerInterface $handler)
    {
        $this->handler = is_string($handler)
            ? $this->handlerFromString($handler)
            : $handler;
    }

    /**
     * Setter.
     */
    public static function handler(string|HandlerInterface $handler): self
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
    public function loader(string|LoaderInterface $loader): self
    {
        $this->loader = is_string($loader)
            ? $this->loaderFromString($loader)
            : $loader;

        return $this;
    }

    /**
     * Setter.
     */
    public function cache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Setter.
     */
    public function resource(ResourceInterface $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Build the collection.
     */
    public function build(): Collection
    {
        return new Collection(
            $this->handler,
            $this->loader ?? $this->makeLoader(),
            $this->name
        );
    }

    /**
     * Resolve the handler from string.
     */
    private function handlerFromString(string $handler): HandlerInterface
    {
        if (! array_key_exists($handler, self::HANDLERS)) {
            throw new InvalidArgumentException(
                sprintf('Handler "%s" not available.', $handler)
            );
        }

        return new DefaultHandler(
            ...array_map(fn ($class) => new $class(), self::HANDLERS[$handler])
        );
    }

    /**
     * Resolve the loader from string.
     */
    private function loaderFromString(string $loader): LoaderInterface
    {
        if (! array_key_exists($loader, self::LOADERS)) {
            throw new InvalidArgumentException(
                sprintf('Loader "%s" not available.', $loader)
            );
        }

        return new DefaultLoader(
            ...array_map(fn ($class) => new $class(), self::LOADERS[$loader])
        );
    }

    /**
     * Make a loader if possible.
     */
    private function makeLoader(): ?LoaderInterface
    {
        return $this->cache === null && $this->resource === null
            ? null
            : new DefaultLoader($this->cache, $this->resource);
    }
}
