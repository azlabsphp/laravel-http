<?php

use Drewlabs\Packages\Http\Factory\AuthorizationErrorResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class AuthorizationErrorResponseFactoryTest extends TestCase
{

    public function test_authorization_error_response_create()
    {
        $factory = new AuthorizationErrorResponseFactory();
        $this->assertInstanceOf(Response::class, ($response = $factory->create(new Request(), new \RuntimeException('Unauthenticated.'))));
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('GET /  Unauthorized access. [ERROR] : Unauthenticated.', $response->getContent());
    }

    public function test_authorization_error_response_create_overrides_default_response_factory()
    {
        $factory = new AuthorizationErrorResponseFactory(function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        });
        $this->assertInstanceOf(JsonResponse::class, ($response = $factory->create(new Request(), new \RuntimeException('Unauthenticated.'))));
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('"GET /  Unauthorized access. [ERROR] : Unauthenticated."', $response->getContent());
    }
}