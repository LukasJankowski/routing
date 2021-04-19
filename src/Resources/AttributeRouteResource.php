<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Resources;

use InvalidArgumentException;
use LukasJankowski\Routing\Attributes\Route;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use RuntimeException;

use const PHP_MAJOR_VERSION;

final class AttributeRouteResource implements RouteResourceInterface
{
    public function __construct(private array $classes)
    {
        if (PHP_MAJOR_VERSION < 8) {
            throw new RuntimeException('Attributes require PHP 8.');
        }
    }

    public function get(): array
    {
        $routes = [];
        foreach ($this->classes as $class) {
            $routes = array_merge($routes, $this->makeRoutesFromClass($class));
        }

        return $routes;
    }

    private function makeRoutesFromClass(string $class): array
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" does not exist.', $class),
                previous: $exception
            );
        }

        $routes = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(Route::class) as $attribute) {
                /** @var \LukasJankowski\Routing\Route $route */
                $route = $attribute->newInstance()->make([$class, $method->getName()]);
                $routes[] = $route;
            }
        }

        return $routes;
    }

}
