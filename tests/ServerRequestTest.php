<?php

use Drewlabs\Core\Helpers\Str;
use Drewlabs\Packages\Http\Exceptions\NotSupportedMessageException;
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
        $this->expectException(NotSupportedMessageException::class);
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

    public function test_get_ips()
    {
        $ips = HttpRequest::ips(new Request());
        $this->assertIsArray($ips);
    }

    public function test_get_ip()
    {
        $ip = HttpRequest::ip(new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']));
        $this->assertEquals('127.0.0.1', $ip);
    }

    public function test_get_server_value()
    {
       $this->assertEquals('127.0.0.1', HttpRequest::server(new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']), 'REMOTE_ADDR'));
       $this->assertNull(HttpRequest::server(new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']), 'HTTP_X_FORWARDED_FOR'));
    }
}