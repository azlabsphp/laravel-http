<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Laravel\Http\Tests;

use Drewlabs\Laravel\Http\Middleware\TrustedIpAddress;
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
        $this->assertInstanceOf(Response::class, $middleware->handle($request, static fn () => new Response(), '192.168.1.65', '127.0.0.1'));
        $this->assertSame($middleware->handle($request, static fn () => new Response(), '192.168.1.65', '127.0.0.1')->getStatusCode(), 200);
    }

    public function test_handle_header_x_real_ip()
    {
        $middleware = new TrustedIpAddress();
        $request = Request::createFromGlobals();
        $request->headers->set('X-Real-IP', '127.0.0.1');
        $this->assertInstanceOf(Response::class, $middleware->handle($request, static fn () => new Response(), '192.168.1.65', '127.0.0.1'));
    }
}
