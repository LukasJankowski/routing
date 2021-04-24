<?php

declare(strict_types=1);

namespace LukasJankowski\Routing;

use LukasJankowski\Routing\Utilities\Method;
use LukasJankowski\Routing\Utilities\Path;
use LukasJankowski\Routing\Utilities\Scheme;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

final class Request
{
    public const METHOD_GET = SymfonyRequest::METHOD_GET;

    public const METHOD_POST = SymfonyRequest::METHOD_POST;

    public const METHOD_PUT = SymfonyRequest::METHOD_PUT;

    public const METHOD_PATCH = SymfonyRequest::METHOD_PATCH;

    public const METHOD_DELETE = SymfonyRequest::METHOD_DELETE;

    public const METHOD_HEAD = SymfonyRequest::METHOD_HEAD;

    public const METHOD_OPTIONS = SymfonyRequest::METHOD_OPTIONS;

    public const METHODS = [
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_PATCH,
        self::METHOD_DELETE,
        self::METHOD_HEAD,
        self::METHOD_OPTIONS,
    ];

    public const SCHEMES = ['HTTPS', 'HTTP', ''];

    public string $method;

    public string $path;

    public string $host;

    public string $scheme;

    /**
     * Request constructor.
     */
    public function __construct(string $method, string $path, string $host, string $scheme)
    {
        $this->method = $method === '' ? self::METHOD_GET : Method::normalize($method);
        $this->path = $path === '' ? '/' : Path::normalize($path);
        if ($this->isCli()) {
            $this->host = $host === '' ? php_uname('n') : $host;
            $this->scheme = '';
        } else {
            $this->host = $host;
            $this->scheme = Scheme::normalize($scheme);
        }
    }

    /**
     * Create request from superglobal variables.
     */
    public static function fromSuperGlobal(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
            $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '',
            $_SERVER['REQUEST_SCHEME'] ?? '',
        );
    }

    /**
     * Create request from PSR7-Request.
     */
    public static function fromPsrRequest(PsrRequest $request): self
    {
        return new self(
            $request->getMethod(),
            $request->getUri()->getPath(),
            $request->getUri()->getHost(),
            $request->getUri()->getScheme()
        );
    }

    /**
     * Create request from symfony request.
     */
    public static function fromSymfonyRequest(SymfonyRequest $request): self
    {
        return new self(
            $request->getMethod(),
            $request->getRequestUri(),
            $request->getHost(),
            $request->getScheme()
        );
    }

    /**
     * Check if the application is running in a CLI context.
     */
    private function isCli(): bool
    {
        return in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true);
    }
}
