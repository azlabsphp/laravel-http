<?php

use Drewlabs\Packages\Http\Middleware\Cors\Contracts\CorsServiceInterface;
use Drewlabs\Packages\Http\Middleware\Cors\CorsService;
use PHPUnit\Framework\TestCase;

class CorsServiceTest extends TestCase
{
    public function test_constructor()
    {
        $service = new CorsService([
            'allowed_hosts' => [
                '*'
            ],
            'allowed_headers' => [],
            "allowed_credentials" => false,
            "exposed_headers" => [],
            "max_age" => 0
        ]);

        $this->assertInstanceOf(CorsServiceInterface::class, $service);
    }

    public function test_is_prelift_request()
    {

        $service = new CorsService([
            'allowed_hosts' => [
                '*'
            ],
            'allowed_headers' => [],
            "allowed_credentials" => false,
            "exposed_headers" => [],
            "max_age" => 0
        ]);
        $request = drewlabs_create_psr7_request();
        $request = $request->withMethod('OPTIONS');
        $request = $request->withHeader('Origin', 'http://localhost');
        $request = $request->withHeader('Access-Control-Request-Method', 'GET');
        $this->assertTrue($service->isPreflightRequest($request));
    }

    public function test_is_cors_request()
    {
        $service = new CorsService([
            'allowed_hosts' => [
                '*'
            ],
            'allowed_headers' => [],
            "allowed_credentials" => false,
            "exposed_headers" => [],
            "max_age" => 0
        ]);
        $request = drewlabs_create_psr7_request();
        $request = $request->withHeader('Origin', 'http://localhost');
        $this->assertTrue($service->isCorsRequest($request));
    }

    public function test_is_cors_request_return_false()
    {

        $service = new CorsService([
            'allowed_hosts' => [
                '*'
            ],
            'allowed_headers' => [],
            "allowed_credentials" => false,
            "exposed_headers" => [],
            "max_age" => 0
        ]);
        $request = drewlabs_create_psr7_request();
        $this->assertFalse($service->isCorsRequest($request));
    }

    public function test_handle_preflight_request()
    {
        $service = new CorsService([
            'allowed_hosts' => [],
            'allowed_headers' => [],
            "allowed_credentials" => false,
            "exposed_headers" => [],
            "max_age" => 0
        ]);
        $request = drewlabs_create_psr7_request();
        $request = $request->withHeader('Origin', 'http://localhost');
        /**
         * @var \Nyholm\Psr7\Response
         */
        $response = $service->handlePreflightRequest($request, new \Nyholm\Psr7\Response());
        $headers = $response->getHeader('Access-Control-Allow-Origin');
        $this->assertEquals('*', array_pop($headers));
    }

    public function test_handle_preflight_request_for_origin()
    {
        $service = new CorsService([
            'allowed_hosts' => [
                'http://localhost'
            ],
            'allowed_headers' => [],
            "allowed_credentials" => false,
            "exposed_headers" => [],
            "max_age" => 0
        ]);
        $request = drewlabs_create_psr7_request();
        $request = $request->withHeader('Origin', 'http://localhost');
        /**
         * @var \Nyholm\Psr7\Response
         */
        $response = $service->handlePreflightRequest($request, new \Nyholm\Psr7\Response());
        $headers = $response->getHeader('Access-Control-Allow-Origin');
        $this->assertEquals('http://localhost', array_pop($headers));
    }

    public function test_normal_request()
    {
        $service = new CorsService([
            'allowed_hosts' => [
                '*'
            ],
            'allowed_headers' => [],
            "allowed_credentials" => false,
            "exposed_headers" => [],
            "max_age" => 0
        ]);
        $request = drewlabs_create_psr7_request();
        // $request = $request->withHeader('Origin', 'http://localhost');
        /**
         * @var \Nyholm\Psr7\Response
         */
        $response = $service->handleNormalRequest($request, new \Nyholm\Psr7\Response());
        $headers = $response->getHeader('Access-Control-Allow-Origin');
        $this->assertEquals('*', array_pop($headers));
    }
}
