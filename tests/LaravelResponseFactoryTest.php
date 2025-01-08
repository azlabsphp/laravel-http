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

use Drewlabs\Laravel\Http\Factory\LaravelResponseFactory;
use Illuminate\Http\Response as HttpResponse;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

class LaravelResponseFactoryTest extends TestCase
{
    public function test_laravel_response_factory_create()
    {
        $factory = new LaravelResponseFactory();
        $response = $factory->create(new Response(200, ['Content-Type' => 'text/html', 'Set-Cookie' => 'sessionId=e8bb43229de9; Expires=Wed, 21 Oct 2015 07:28:00 GMT; Domain=foo.example.com; Path=/; Secure; HttpOnly']));

        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
        $cookie = $response->headers->getCookies()[0];
        if ($cookie) {
            $this->assertSame('sessionId', $cookie->getName());
            $this->assertSame('e8bb43229de9', $cookie->getValue());
            $this->assertSame('foo.example.com', $cookie->getDomain());
        }
    }

    public function test_laravel_response_factory_create_from_attributes()
    {
        $factory = new LaravelResponseFactory();
        $response = $factory->create(null, 200, ['Content-Type' => 'text/html']);

        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->headers->get('Content-Type'));
        $this->assertSame('1.1', $response->getProtocolVersion());
    }
}
