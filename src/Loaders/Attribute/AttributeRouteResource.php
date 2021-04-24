<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Attribute;

use InvalidArgumentException;
use LukasJankowski\Routing\Attributes\Route;
use LukasJankowski\Routing\Loaders\RouteResourceInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

final class AttributeRouteResource implements RouteResourceInterface
{
    /**
     * AttributeRouteResource constructor.
     *
     * @param array<string> $classes
     */
    public function __construct(private array $classes)
    {
    }

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        $routes = [];
        foreach ($this->classes as $class) {
            $routes = array_merge($routes, $this->makeRoutesFromClass($class));
        }

        return $routes;
    }

    /**
     * Retrieve routes from attributes on methods in class.
     */
    private function makeRoutesFromClass(string $class): array
    {
        $routes = [];
        foreach ($this->getMethodsFromClass($class) as $method) {
            foreach ($method->getAttributes(Route::class) as $attribute) {
                /** @var \LukasJankowski\Routing\Route $route */
                $route = $attribute->newInstance()->make([$class, $method->getName()]);
                $routes[] = $route;
            }
        }

        return $routes;
    }

    /**
     * Extract methods from class.
     */
    private function getMethodsFromClass(string $class): array
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" does not exist.', $class),
                previous: $exception
            );
        }

        return $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    }
}
