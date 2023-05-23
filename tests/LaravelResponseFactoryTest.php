<?php

use Drewlabs\Packages\Http\Factory\LaravelResponseFactory;
use Illuminate\Http\Response as HttpResponse;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

class LaravelResponseFactoryTest extends TestCase
{

    public function test_laravel_response_factory_create()
    {
        $factory = new LaravelResponseFactory;
        $response = $factory->create(new Response(200, ['Content-Type' => 'text/html', 'Set-Cookie' => 'sessionId=e8bb43229de9; Expires=Wed, 21 Oct 2015 07:28:00 GMT; Domain=foo.example.com; Path=/; Secure; HttpOnly']));

        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/html', $response->headers->get('Content-Type'));
        $cookie = $response->headers->getCookies()[0];
        if ($cookie) {
            $this->assertEquals('sessionId', $cookie->getName());
            $this->assertEquals('e8bb43229de9', $cookie->getValue());
            $this->assertEquals('foo.example.com', $cookie->getDomain());
        }
    }


    public function test_laravel_response_factory_create_from_attributes()
    {
        $factory = new LaravelResponseFactory;
        $response = $factory->create(null, 200, ['Content-Type' => 'text/html']);

        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/html', $response->headers->get('Content-Type'));
        $this->assertEquals('1.1', $response->getProtocolVersion());
    }
}
