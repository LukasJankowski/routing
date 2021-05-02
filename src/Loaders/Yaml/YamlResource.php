<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Yaml;

use ErrorException;
use InvalidArgumentException;
use LukasJankowski\Routing\Loaders\ResourceInterface;
use LukasJankowski\Routing\Route;
use LukasJankowski\Routing\RouteBuilder;
use Symfony\Component\Yaml\Yaml;

final class YamlResource implements ResourceInterface
{
    private string $file;

    /** @var array<Route> */
    private array $routes = [];

    /**
     * YamlResource constructor.
     */
    public function __construct(string $file)
    {
        if (! file_exists($file)) {
            throw new InvalidArgumentException(sprintf('The file "%s" could not be found.', $file));
        }

        $this->file = $file;
    }

    /**
     * @inheritdoc
     *
     * @throws ErrorException
     */
    public function get(): array
    {
        try {
            $routes = Yaml::parseFile($this->file);
        } catch (\Exception $exception) {
            throw new ErrorException(
                sprintf('The file "%s" is not valid.', $this->file),
                previous: $exception
            );
        }

        $this->iterateRoutes($routes);

        return $this->routes;
    }

    /**
     * Iterate over the routes.
     */
    private function iterateRoutes(array $routes): void
    {
        foreach ($routes as $path => $properties) {
            ! is_string($path) ? $this->buildGroup($properties) : $this->buildRoute($path, $properties);
        }
    }

    /**
     * Build a group from the parsed attributes.
     *
     * @param array<string,mixed> $properties
     */
    private function buildGroup(array $properties): void
    {
        $routes = $properties['group'];
        RouteBuilder::group($properties, function () use ($routes) {
            $this->iterateRoutes($routes);
        });
    }

    /**
     * Build a route from the parsed properties.
     *
     * @param array<string,mixed> $properties
     */
    private function buildRoute(string $path, array $properties): void
    {
        $builder = RouteBuilder::match((array) $properties['method'], $path);

        $methods = ['action', 'name', 'host', 'scheme', 'constraint', 'middleware', 'default'];

        foreach ($methods as $method) {
            if (isset($properties[$method])) {
                $builder->$method($properties[$method]);
            }
        }

        $this->routes[] = $builder->build();
    }
}
