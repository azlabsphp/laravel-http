<?php

use Drewlabs\Cors\Cors;
use Drewlabs\Packages\Http\Middleware\Cors as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class CorsMiddlewareTest extends TestCase
{

    public function test_cors_request()
    {
        $config = require __DIR__ . '/../src/config/http.php';
        $middleware = new Middleware(new Cors($config['cors'] ?? []));
        $request = Request::create('http://127.0.0.1:8000/api/posts', 'POST');
        $request->headers->set('Origin', 'http://localhost:4200');
        $response = $middleware->handle($request, function() {
            return new Response(null, 422);
        });
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('http://localhost:4200', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertEquals('true', $response->headers->get('Access-Control-Allow-Credentials'));
    }

    public function test_preflight_request()
    {
        $config = require __DIR__ . '/../src/config/http.php';
        $middleware = new Middleware(new Cors($config['cors'] ?? []));
        $request = Request::create('http://127.0.0.1:8000/api/posts', 'OPTIONS');
        $request->headers->set('Origin', 'http://localhost:4200');
        $request->headers->set('Access-Control-Request-Method', 'POST');
        $response = $middleware->handle($request, function() {
            return new Response(null, 422);
        });
        $this->assertNotEquals(422, $response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('http://localhost:4200', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertEquals('true', $response->headers->get('Access-Control-Allow-Credentials'));

    }
}