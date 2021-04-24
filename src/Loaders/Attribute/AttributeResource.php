<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Attribute;

use InvalidArgumentException;
use LukasJankowski\Routing\Attributes\Group;
use LukasJankowski\Routing\Attributes\Route;
use LukasJankowski\Routing\Loaders\ResourceInterface;
use LukasJankowski\Routing\RouteBuilder;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

final class AttributeResource implements ResourceInterface
{
    /**
     * AttributeResource constructor.
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
        $class = $this->getClass($class);
        /** @var ReflectionAttribute $group */
        $group = $class->getAttributes(Group::class);

        if (! empty($group)) {
            $routes = [];
            RouteBuilder::group(
                $group[0]->newInstance()->properties(),
                function () use ($class, &$routes) {
                    $routes = $this->getRoutes($class);
                }
            );

            return $routes;
        }

        return $this->getRoutes($class);
    }

    /**
     * Extract methods from class.
     */
    private function getClass(string $class): ReflectionClass
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" does not exist.', $class),
                previous: $exception
            );
        }
    }

    /**
     * Get the routes from the class.
     */
    public function getRoutes(ReflectionClass $class): array
    {
        $routes = [];
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(Route::class) as $attribute) {
                /** @var \LukasJankowski\Routing\Route $route */
                $route = $attribute->newInstance()->make([$class->getName(), $method->getName()]);
                $routes[] = $route;
            }
        }

        return $routes;
    }
}
