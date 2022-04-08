<?php

use Drewlabs\Core\Helpers\Str;
use Drewlabs\Packages\Http\Exceptions\UnsupportedTypeException;
use Drewlabs\Packages\Http\Facades\HttpRequest;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    public function test_get_header()
    {
        $request = drewlabs_create_psr7_request();
        $this->assertNull(HttpRequest::getHeader($request, 'Authorization'));
        $key = Str::md5();
        $request = $request->withHeader('Authorization', 'Bearer ft_' . $key);
        $this->assertEquals('Bearer ft_' . $key, HttpRequest::getHeader($request, 'Authorization'));

        // Test for Symfony Request
        $symfonyRequest = new Request();
        $symfonyRequest->headers->set('Authorization', 'Bearer ft_' . $key);
        $this->assertEquals('Bearer ft_' . $key, HttpRequest::getHeader($symfonyRequest, 'Authorization'));
    }

    public function test_is_supported()
    {
        $request = drewlabs_create_psr7_request();
        $this->assertTrue(HttpRequest::isSupported($request));
        // Test for Symfony Request
        $symfonyRequest = new Request();
        $this->assertTrue(HttpRequest::isSupported($symfonyRequest));
        $this->expectException(UnsupportedTypeException::class);
        $this->assertFalse(HttpRequest::isSupported(new \stdClass));
        $this->assertFalse(HttpRequest::isSupported(null));

    }

    public function test_get_method()
    {
        $request = drewlabs_create_psr7_request();
        $this->assertEquals(HttpRequest::getMethod($request), $request->getMethod());
        // Test for Symfony Request
        $symfonyRequest = new Request();
        $symfonyRequest->setMethod('POST');
        $this->assertEquals(HttpRequest::getMethod($symfonyRequest), 'POST');
    }

    public function test_is_method()
    {
        $request = drewlabs_create_psr7_request();
        $this->assertTrue(HttpRequest::isMethod($request, 'GET'));
        // Test for Symfony Request
        $symfonyRequest = new Request();
        $symfonyRequest->setMethod('POST');
        $this->assertTrue(HttpRequest::isMethod($symfonyRequest, 'POST'));
    }
}