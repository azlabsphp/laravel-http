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

use Drewlabs\Cors\Cors;
use Drewlabs\Laravel\Http\Middleware\Cors as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class CorsMiddlewareTest extends TestCase
{
    public function test_cors_request()
    {
        $config = require __DIR__.'/../src/config/http.php';
        $middleware = new Middleware(new Cors($config['cors'] ?? []));
        $request = Request::create('http://127.0.0.1:8000/api/posts', 'POST');
        $request->headers->set('Origin', 'http://localhost:4200');
        $response = $middleware->handle($request, static function () {
            return new Response(null, 422);
        });
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('http://localhost:4200', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertSame('true', $response->headers->get('Access-Control-Allow-Credentials'));
    }

    public function test_preflight_request()
    {
        $config = require __DIR__.'/../src/config/http.php';
        $middleware = new Middleware(new Cors($config['cors'] ?? []));
        $request = Request::create('http://127.0.0.1:8000/api/posts', 'OPTIONS');
        $request->headers->set('Origin', 'http://localhost:4200');
        $request->headers->set('Access-Control-Request-Method', 'POST');
        $response = $middleware->handle($request, static function () {
            return new Response(null, 422);
        });
        $this->assertNotSame(422, $response->getStatusCode());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('http://localhost:4200', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertSame('true', $response->headers->get('Access-Control-Allow-Credentials'));
    }
}
