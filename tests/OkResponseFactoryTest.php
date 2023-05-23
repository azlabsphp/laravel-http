<?php

use Drewlabs\Packages\Http\Factory\OkResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class OkResponseFactoryTest extends TestCase
{
    public function test_ok_response_create()
    {
        $factory = new OkResponseFactory();
        $this->assertInstanceOf(Response::class, ($response = $factory->create([], ['Content-Type' => 'application/json', 'Accept' => '*/*'])));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_ok_response_create_overrides_default_response_factory()
    {
        $factory = new OkResponseFactory(function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        });
        $this->assertInstanceOf(JsonResponse::class, ($response = $factory->create(['data' => []], ['Content-Type' => 'application/json', 'Accept' => '*/*'])));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['data' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

}