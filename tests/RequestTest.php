<?php

use LukasJankowski\Routing\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private string $host;

    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->host = php_uname('n');
        $this->path = '/';
    }

    public function test_it_can_be_created_from_superglobal()
    {
        $this->assertEquals(
            new Request('GET', $this->path, $this->host, ''),
            Request::fromSuperGlobal()
        );
    }

    public function test_it_can_be_created_from_psr_request()
    {
        $this->assertEquals(
            new Request('GET', $this->path, $this->host, ''),
            Request::fromPsrRequest(new \Nyholm\Psr7\ServerRequest('GET', $this->path))
        );
    }

    public function test_it_can_be_created_from_symfony_request()
    {
        $this->assertEquals(
            new Request('GET', $this->path, $this->host, ''),
            Request::fromSymfonyRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals())
        );
    }

    public function test_it_can_be_created_faking_environment()
    {
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_SERVER['REQUEST_URI'] = '/path/to/resource';
        $_SERVER['SERVER_NAME'] = 'api.test.com';
        $_SERVER['REQUEST_SCHEME'] = 'https';

        $request = new Request('POST', '/path/to/resource', 'api.test.com', 'HTTPS');

        $this->assertEquals($request, Request::fromSuperGlobal());

        $this->assertEquals(
            $request,
            Request::fromPsrRequest(
                new \Nyholm\Psr7\ServerRequest('post', 'https://api.test.com/path/to/resource')
            )
        );

        $this->assertEquals(
            $request,
            Request::fromSymfonyRequest(
                \Symfony\Component\HttpFoundation\Request::createFromGlobals()
            )
        );
    }
}
