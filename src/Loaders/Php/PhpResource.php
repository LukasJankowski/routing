<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Loaders\Php;

use ErrorException;
use InvalidArgumentException;
use LukasJankowski\Routing\Loaders\ResourceInterface;
use LukasJankowski\Routing\RouteBuilder;

class PhpResource implements ResourceInterface
{
    private string $file;

    /**
     * PhpResource constructor.
     */
    public function __construct(string $file)
    {
        if (! file_exists($file)) {
            throw new InvalidArgumentException(sprintf('The file "%s" could not be found.', $file));
        }

        $this->file = $file;
    }

    /**
     * @inheritDoc
     *
     * @throws ErrorException
     */
    public function get(): array
    {
        $routes = require $this->file;

        if (RouteBuilder::usesStaticCollection()) {
            return RouteBuilder::fromStaticCollection();
        }

        if (! is_array($routes)) {
            throw new ErrorException(
                sprintf('The file "%s" must contain an array of routes or use static collection.', $this->file)
            );
        }

        return $routes;
    }
}
