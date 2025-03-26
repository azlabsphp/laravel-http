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

use Drewlabs\Core\Helpers\Str;
use Drewlabs\Laravel\Http\Facades\HttpRequest;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    public function test_get_header()
    {
        $key = Str::md5();
        // Test for Symfony Request
        $symfonyRequest = new Request();
        $symfonyRequest->headers->set('Authorization', 'Bearer ft_'.$key);
        $this->assertSame('Bearer ft_'.$key, HttpRequest::getHeader($symfonyRequest, 'Authorization'));
    }

    public function test_get_method()
    {
        $symfonyRequest = new Request();
        $symfonyRequest->setMethod('POST');
        $this->assertSame(HttpRequest::getMethod($symfonyRequest), 'POST');
    }

    public function test_is_method()
    {
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
        $this->assertSame('127.0.0.1', $ip);
    }

    public function test_get_server_value()
    {
        $this->assertSame('127.0.0.1', HttpRequest::server(new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']), 'REMOTE_ADDR'));
        $this->assertNull(HttpRequest::server(new Request([], [], [], [], [], ['REMOTE_ADDR' => '127.0.0.1']), 'HTTP_X_FORWARDED_FOR'));
    }
}
