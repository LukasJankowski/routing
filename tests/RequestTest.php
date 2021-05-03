<?php

use LukasJankowski\Routing\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new Request('GET', '/', php_uname('n'), '');
    }

    public function test_it_can_be_created_from_superglobal()
    {
        $this->assertEquals($this->request, Request::fromSuperGlobal());
    }

    public function test_it_can_be_created_from_psr_request()
    {
        $this->assertEquals(
            $this->request,
            Request::fromPsrRequest(new \Nyholm\Psr7\ServerRequest('GET', $this->request->path))
        );
    }

    public function test_it_can_be_created_from_symfony_request()
    {
        $this->assertEquals(
            $this->request,
            Request::fromSymfonyRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals())
        );
    }

    public function test_it_can_be_created_with_a_faked_environment()
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
