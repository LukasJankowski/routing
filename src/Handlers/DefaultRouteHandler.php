<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

final class DefaultRouteHandler implements RouteHandlerInterface
{
    /**
     * DefaultRouteHandler constructor.
     */
    public function __construct(private RouteMatcherInterface $matcher, private RouteParserInterface $parser)
    {
    }

    /**
     * @inheritDoc
     */
    public function matches(Route $route, Request $request): bool
    {
        return $this->matcher->matches($route, $request);
    }

    /**
     * @inheritdoc
     */
    public function parse(array $routes): array
    {
        return $this->parser->parse($routes);
    }
}
