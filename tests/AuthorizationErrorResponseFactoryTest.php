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

use Drewlabs\Laravel\Http\Factory\AuthorizationErrorResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class AuthorizationErrorResponseFactoryTest extends TestCase
{
    public function test_authorization_error_response_create()
    {
        $factory = new AuthorizationErrorResponseFactory();
        $this->assertInstanceOf(Response::class, $response = $factory->create(new Request(), new \RuntimeException('Unauthenticated.')));
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('GET /  Unauthorized access. [ERROR] : Unauthenticated.', $response->getContent());
    }

    public function test_authorization_error_response_create_overrides_default_response_factory()
    {
        $factory = new AuthorizationErrorResponseFactory(static function ($data = null, $status = 200, $headers = []) {
            return new JsonResponse($data, $status, $headers, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
        });
        $this->assertInstanceOf(JsonResponse::class, $response = $factory->create(new Request(), new \RuntimeException('Unauthenticated.')));
        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('"GET /  Unauthorized access. [ERROR] : Unauthenticated."', $response->getContent());
    }
}
