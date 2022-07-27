<?php

use Drewlabs\Packages\Http\Middleware\TrustedIpAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class TrustedIpAddressTest extends TestCase
{
    public function test_handle_server_remote_addr()
    {
        $middleware = new TrustedIpAddress();
        $request = Request::createFromGlobals();
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $this->assertInstanceOf(Response::class, $middleware->handle($request, fn () => new Response(), '192.168.1.65', '127.0.0.1'));
        $this->assertEquals($middleware->handle($request, fn () => new Response(), '192.168.1.65', '127.0.0.1')->getStatusCode(), 200);
    }


    public function test_handle_header_x_real_ip()
    {
        $middleware = new TrustedIpAddress();
        $request = Request::createFromGlobals();
        $request->headers->set('X-Real-IP', '127.0.0.1');
        $this->assertInstanceOf(Response::class, $middleware->handle($request, fn () => new Response(), '192.168.1.65', '127.0.0.1'));
    }
}
