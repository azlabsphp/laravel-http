<?php

use Drewlabs\Packages\Http\Factory\BadRequestResponseFactory;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Response;

class BadRequestResponseFactoryTest extends TestCase
{
    public function test_server_error_response_create()
    {
        $factory = new BadRequestResponseFactory();
        $this->assertInstanceOf(Response::class, ($response = $factory->create([])));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('[]', $response->getContent());
    }

    public function test_server_error_response_create_overrides_default_response_factory()
    {
        $factory = new BadRequestResponseFactory(function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        });
        $this->assertInstanceOf(JsonResponse::class, ($response = $factory->create(['name' => ['name attribute is required']])));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(json_encode(['name' => ['name attribute is required']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), $response->getContent());
    }

}