<?php

use Drewlabs\Packages\Http\Factory\ServerErrorResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class ServerErrorResponseFactoryTest extends TestCase
{
    public function test_server_error_response_create()
    {
        $factory = new ServerErrorResponseFactory();
        $this->assertInstanceOf(Response::class, ($response = $factory->create(new \RuntimeException('Server Error.'))));
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Server Error.', $response->getContent());
    }

    public function test_server_error_response_create_overrides_default_response_factory()
    {
        $factory = new ServerErrorResponseFactory(function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        });
        $this->assertInstanceOf(JsonResponse::class, ($response = $factory->create(new \RuntimeException('Server Error.'))));
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('"Server Error."', $response->getContent());
    }
}