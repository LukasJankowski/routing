<?php

declare(strict_types=1);

namespace LukasJankowski\Routing\Handlers;

use LukasJankowski\Routing\Request;
use LukasJankowski\Routing\Route;

final class DefaultHandler implements HandlerInterface
{
    /**
     * DefaultHandler constructor.
     */
    public function __construct(private MatcherInterface $matcher, private ParserInterface $parser)
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
